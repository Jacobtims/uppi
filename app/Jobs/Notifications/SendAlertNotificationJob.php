<?php

namespace App\Jobs\Notifications;

use App\Models\Anomaly;
use App\Models\AlertTrigger;
use App\Notifications\MonitorDownNotification;
use App\Enums\AlertTriggerType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAlertNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Anomaly $anomaly
    ) {}

    public function handle(): void
    {
        $monitor = $this->anomaly->monitor;
        $alert = $this->anomaly->alert;
        $user = $monitor->user;

        if (!$alert || !$alert->is_enabled) {
            return;
        }

        // Create alert trigger record
        $trigger = new AlertTrigger([
            'anomaly_id' => $this->anomaly->id,
            'alert_id' => $alert->id,
            'monitor_id' => $monitor->id,
            'type' => AlertTriggerType::DOWN,
            'channels_notified' => [$alert->type],
            'metadata' => [
                'monitor_name' => $monitor->name,
                'monitor_target' => $monitor->address,
                'last_check_output' => $this->anomaly->checks->last()?->output,
            ],
            'triggered_at' => now(),
        ]);
        $trigger->save();

        // Send notification to the user
        $user->notify(new MonitorDownNotification($this->anomaly));
    }
}
