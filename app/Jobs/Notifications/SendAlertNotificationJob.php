<?php

namespace App\Jobs\Notifications;

use App\Enums\Alerts\AlertTriggerType;
use App\Models\Alert;
use App\Models\AlertTrigger;
use App\Models\Anomaly;
use App\Notifications\MonitorDownNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAlertNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Anomaly $anomaly,
        protected Alert   $alert
    )
    {
    }

    public function handle(): void
    {
        $monitor = $this->anomaly->monitor;

        if (!$this->alert->is_enabled) {
            return;
        }

        // Create alert trigger record
        $trigger = new AlertTrigger([
            'anomaly_id' => $this->anomaly->id,
            'alert_id' => $this->alert->id,
            'monitor_id' => $monitor->id,
            'type' => AlertTriggerType::DOWN,
            'channels_notified' => [$this->alert->type],
            'metadata' => [
                'monitor_name' => $monitor->name,
                'monitor_target' => $monitor->address,
                'last_check_output' => $this->anomaly->checks->last()?->output,
            ],
            'triggered_at' => now(),
        ]);
        $trigger->save();

        // Send notification to the alert
        $this->alert->notify(new MonitorDownNotification($this->anomaly));
    }
}
