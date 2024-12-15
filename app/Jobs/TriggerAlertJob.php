<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Anomaly;
use App\Jobs\Notifications\SendAlertNotificationJob;
use App\Jobs\Notifications\SendRecoveryNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\Checks\Status;
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
        return 'trigger_alert_' . $this->check->id;
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

    protected function handleMonitorDown($monitor): void
    {
        DB::transaction(function () use ($monitor) {
            // Check if there's already an active anomaly
            $activeAnomaly = $monitor->anomalies()
                ->whereNull('ended_at')
                ->lockForUpdate()
                ->first();

            if (!$activeAnomaly) {
                // Create new anomaly if none exists
                $anomaly = new Anomaly([
                    'started_at' => $this->check->checked_at,
                    'monitor_id' => $monitor->id,
                ]);
                $anomaly->save();

                // Associate check with anomaly
                $this->check->anomaly()->associate($anomaly);
                $this->check->save();

                // Notify each enabled alert
                foreach ($monitor->alerts as $alert) {
                    if (!$alert->is_enabled) {
                        continue;
                    }

                    SendAlertNotificationJob::dispatch($anomaly, $alert);
                }
            } else {
                // Associate check with existing anomaly
                $this->check->anomaly()->associate($activeAnomaly);
                $this->check->save();
            }
        });
    }

    protected function handleMonitorRecovery($monitor): void
    {
        DB::transaction(function () use ($monitor) {
            // If status is OK, check if we need to close any anomalies
            $activeAnomaly = $monitor->anomalies()
                ->whereNull('ended_at')
                ->lockForUpdate()
                ->first();

            if ($activeAnomaly) {
                // Set the ended_at to the time of this check
                $activeAnomaly->ended_at = $this->check->checked_at;
                $activeAnomaly->save();

                // Associate this recovery check with the anomaly
                $this->check->anomaly()->associate($activeAnomaly);
                $this->check->save();

                // Notify each enabled alert
                foreach ($monitor->alerts as $alert) {
                    if (!$alert->is_enabled) {
                        continue;
                    }

                    SendRecoveryNotificationJob::dispatch($activeAnomaly, $alert);
                }
            }
        });
    }
}
