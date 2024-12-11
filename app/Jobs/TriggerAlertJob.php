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

class TriggerAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Check $check
    ) {}

    public function handle(): void
    {
        $monitor = $this->check->monitor;

        // Process each alert attached to the monitor
        foreach ($monitor->alerts as $alert) {
            if (!$alert->is_enabled) {
                continue;
            }

            if ($this->check->status === Status::FAIL) {
                $this->handleMonitorDown($monitor, $alert);
            } else {
                $this->handleMonitorRecovery($monitor, $alert);
            }
        }
    }

    protected function handleMonitorDown($monitor, $alert): void
    {
        // Check if there's already an active anomaly for this alert
        $activeAnomaly = $monitor->anomalies()
            ->where('alert_id', $alert->id)
            ->whereNull('ended_at')
            ->first();

        if (!$activeAnomaly) {
            // Create new anomaly if none exists
            $anomaly = new Anomaly([
                'started_at' => $this->check->checked_at,
                'monitor_id' => $monitor->id,
                'alert_id' => $alert->id,
            ]);
            $anomaly->save();

            // Associate check with anomaly
            $this->check->anomaly()->associate($anomaly);
            $this->check->save();

            // Dispatch notification job
            SendAlertNotificationJob::dispatch($anomaly);
        } else {
            // Associate check with existing anomaly
            $this->check->anomaly()->associate($activeAnomaly);
            $this->check->save();
        }
    }

    protected function handleMonitorRecovery($monitor, $alert): void
    {
        // If status is OK, check if we need to close any anomalies for this alert
        $activeAnomaly = $monitor->anomalies()
            ->where('alert_id', $alert->id)
            ->whereNull('ended_at')
            ->first();

        if ($activeAnomaly) {
            // Set the ended_at to the time of this check
            $activeAnomaly->ended_at = $this->check->checked_at;
            $activeAnomaly->save();

            // Associate this recovery check with the anomaly
            $this->check->anomaly()->associate($activeAnomaly);
            $this->check->save();

            // Dispatch recovery notification
            SendRecoveryNotificationJob::dispatch($activeAnomaly);
        }
    }
}
