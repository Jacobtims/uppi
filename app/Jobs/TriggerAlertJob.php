<?php

namespace App\Jobs;

use App\Enums\Checks\Status;
use App\Jobs\Notifications\SendAlertNotificationJob;
use App\Jobs\Notifications\SendRecoveryNotificationJob;
use App\Models\Alert;
use App\Models\Anomaly;
use App\Models\Check;
use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TriggerAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uniqueFor = 60;

    public function __construct(
        protected Check $check
    ) {}

    public function uniqueId(): string
    {
        return 'trigger_alert_'.$this->check->id;
    }

    public function handle(): void
    {
        $monitor = $this->check->monitor;

        if ($this->check->status === Status::FAIL) {
            $this->handleMonitorDown($monitor);
        } else {
            $this->handleMonitorRecovery($monitor);
        }
    }

    protected function handleMonitorDown(Monitor $monitor): void
    {
        DB::transaction(function () use ($monitor) {
            $activeAnomaly = $this->getActiveAnomaly($monitor);
            $recentChecks = $this->getRecentChecks($monitor);

            if (! $activeAnomaly && $this->hasMetFailureThreshold($recentChecks)) {
                $anomaly = $this->createAnomaly($monitor, $recentChecks);
                $this->associateChecksWithAnomaly($monitor, $recentChecks, $anomaly);
                $this->notifyAlerts($monitor, $anomaly, SendAlertNotificationJob::class);
            } elseif ($activeAnomaly) {
                $this->associateCheckWithAnomaly($activeAnomaly);
            }
        });
    }

    protected function handleMonitorRecovery(Monitor $monitor): void
    {
        DB::transaction(function () use ($monitor) {
            $recentChecks = $this->getRecentChecks($monitor);

            if ($this->hasMetRecoveryThreshold($recentChecks)) {
                $activeAnomaly = $this->getActiveAnomaly($monitor);

                if ($activeAnomaly) {
                    $this->closeAnomaly($activeAnomaly, $recentChecks);
                    $this->associateChecksWithAnomaly($monitor, $recentChecks, $activeAnomaly);
                    $this->notifyAlerts($monitor, $activeAnomaly, SendRecoveryNotificationJob::class);
                }
            }
        });
    }

    protected function getActiveAnomaly(Monitor $monitor): ?Anomaly
    {
        return $monitor->anomalies()
            ->whereNull('ended_at')
            ->lockForUpdate()
            ->first();
    }

    protected function getRecentChecks(Monitor $monitor): Collection
    {
        return $monitor->checks()
            ->latest('checked_at')
            ->take($monitor->consecutive_threshold)
            ->get();
    }

    protected function hasMetFailureThreshold(Collection $checks): bool
    {
        return $this->hasMetThreshold($checks, Status::FAIL);
    }

    protected function hasMetRecoveryThreshold(Collection $checks): bool
    {
        return $this->hasMetThreshold($checks, Status::OK);
    }

    protected function hasMetThreshold(Collection $checks, Status $status): bool
    {
        $threshold = $this->check->monitor->consecutive_threshold;

        return $checks->count() >= $threshold &&
            $checks->every(fn ($check) => $check->status === $status);
    }

    protected function createAnomaly(Monitor $monitor, Collection $checks): Anomaly
    {
        $anomaly = new Anomaly([
            'started_at' => $checks->last()->checked_at,
            'monitor_id' => $monitor->id,
        ]);

        $anomaly->save();

        return $anomaly;
    }

    protected function closeAnomaly(Anomaly $anomaly, Collection $checks): void
    {
        $anomaly->ended_at = $checks->last()->checked_at;
        $anomaly->save();
    }

    protected function associateChecksWithAnomaly(Monitor $monitor, Collection $checks, Anomaly $anomaly): void
    {
        $status = $this->check->status;

        $monitor->checks()
            ->where('checked_at', '>=', $checks->last()->checked_at)
            ->where('status', $status)
            ->update(['anomaly_id' => $anomaly->id]);
    }

    protected function associateCheckWithAnomaly(Anomaly $anomaly): void
    {
        $this->check->anomaly()->associate($anomaly);
        $this->check->save();
    }

    protected function notifyAlerts(Monitor $monitor, Anomaly $anomaly, string $jobClass): void
    {
        $monitor->alerts
            ->filter->is_enabled
            ->each(fn (Alert $alert) => dispatch(new $jobClass($anomaly, $alert)));
    }
}
