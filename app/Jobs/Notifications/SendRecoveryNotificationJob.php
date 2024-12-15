<?php

namespace App\Jobs\Notifications;

use App\Models\Anomaly;
use App\Models\Alert;
use App\Models\AlertTrigger;
use App\Notifications\MonitorRecoveredNotification;
use App\Enums\AlertTriggerType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRecoveryNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Anomaly $anomaly,
        protected Alert $alert
    ) {}

    public function handle(): void
    {
        $monitor = $this->anomaly->monitor;

        if (!$this->alert->is_enabled) {
            return;
        }

        // Calculate downtime duration
        $duration = $this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true);

        // Create alert trigger record
        $trigger = new AlertTrigger([
            'anomaly_id' => $this->anomaly->id,
            'alert_id' => $this->alert->id,
            'monitor_id' => $monitor->id,
            'type' => AlertTriggerType::RECOVERY,
            'channels_notified' => [$this->alert->type],
            'metadata' => [
                'monitor_name' => $monitor->name,
                'monitor_target' => $monitor->address,
                'downtime_duration' => $duration,
                'started_at' => $this->anomaly->started_at->format('Y-m-d H:i:s'),
                'ended_at' => $this->anomaly->ended_at->format('Y-m-d H:i:s'),
            ],
            'triggered_at' => now(),
        ]);
        $trigger->save();

        // Send notification to the alert
        $this->alert->notify(new MonitorRecoveredNotification($this->anomaly));
    }
}
