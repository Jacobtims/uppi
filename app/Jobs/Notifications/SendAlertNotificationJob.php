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

        if (!$alert || !$alert->is_enabled) {
            return;
        }

        // Get list of notification channels
        $channels = $alert->notificationChannels;

        // Create alert trigger record
        $trigger = new AlertTrigger([
            'anomaly_id' => $this->anomaly->id,
            'alert_id' => $alert->id,
            'monitor_id' => $monitor->id,
            'type' => AlertTriggerType::DOWN,
            'channels_notified' => $channels->pluck('type')->toArray(),
            'metadata' => [
                'monitor_name' => $monitor->name,
                'monitor_target' => $monitor->target,
                'last_check_output' => $this->anomaly->checks->last()?->output,
            ],
            'triggered_at' => now(),
        ]);
        $trigger->save();

        // Send notification to all alert channels
        $channels->each(function ($channel) {
            $channel->notify(new MonitorDownNotification($this->anomaly));
        });
    }
}
