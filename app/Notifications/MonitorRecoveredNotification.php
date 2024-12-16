<?php

namespace App\Notifications;

use App\Models\Anomaly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;
use NotificationChannels\Bird\BirdMessage;
use NotificationChannels\Messagebird\MessagebirdMessage;
use NotificationChannels\Pushover\PushoverMessage;

class MonitorRecoveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Anomaly $anomaly
    ) {}

    public function via(object $notifiable): array
    {
        return [$notifiable->type->toNotificationChannel()];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $monitor = $this->anomaly->monitor;
        $duration = $this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true);

        return (new MailMessage)
            ->success()
            ->subject("✅ Monitor Recovered: {$monitor->name}")
            ->greeting("The monitor {$monitor->name} is back UP!")
            ->line("Target: {$monitor->address}")
            ->line("Downtime duration: {$duration}")
            ->line("Recovered at: {$this->anomaly->ended_at->format('Y-m-d H:i:s')}")
            ->action('Open '.config('app.name'), url('/'))
            ->line('Thank you for using '.config('app.name').'!');
    }

    public function toMessagebird(object $notifiable): MessagebirdMessage
    {
        $downTimeShort = $this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true);

        return new MessagebirdMessage("✅ Monitor Recovered: {$this->anomaly->monitor->name} ({$this->anomaly->monitor->address}): {$downTimeShort}");
    }

    public function toBird(object $notifiable): BirdMessage
    {
        $downTimeShort = $this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true);

        return new BirdMessage("✅ Monitor Recovered: {$this->anomaly->monitor->name} ({$this->anomaly->monitor->address}): {$downTimeShort}");
    }

    public function toPushover(object $notifiable): PushoverMessage
    {
        return PushoverMessage::create()
            ->title("✅ Monitor Recovered: {$this->anomaly->monitor->name} ({$this->anomaly->monitor->address})")
            ->content("The monitor {$this->anomaly->monitor->name} is back UP and recovered after {$this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true)}")
            ->highPriority()
            ->url(url('/'), 'Open '.config('app.name'));
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        $monitor = $this->anomaly->monitor;
        $duration = $this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true);
        $lastCheck = $this->anomaly->checks->last();

        $template = <<<JSON
        {
            "blocks": [
                {
                    "type": "header",
                    "text": {
                        "type": "plain_text",
                        "text": "✅ Monitor Recovered: {$monitor->name}",
                        "emoji": true
                    }
                },
                {
                    "type": "section",
                    "fields": [
                        {
                            "type": "mrkdwn",
                            "text": "*Type:*\\n{$monitor->type->value}"
                        },
                        {
                            "type": "mrkdwn",
                            "text": "*Target:*\\n{$monitor->address}"
                        }
                    ]
                },
                {
                    "type": "section",
                    "fields": [
                        {
                            "type": "mrkdwn",
                            "text": "*Downtime Duration:*\\n{$duration}"
                        },
                        {
                            "type": "mrkdwn",
                            "text": "*Response Time:*\\n{$lastCheck?->response_time}ms"
                        }
                    ]
                },
                {
                    "type": "section",
                    "fields": [
                        {
                            "type": "mrkdwn",
                            "text": "*Started At:*\\n{$this->anomaly->started_at->format('Y-m-d H:i:s')}"
                        },
                        {
                            "type": "mrkdwn",
                            "text": "*Recovered At:*\\n{$this->anomaly->ended_at->format('Y-m-d H:i:s')}"
                        }
                    ]
                }
            ]
        }
        JSON;

        return (new SlackMessage)->usingBlockKitTemplate($template);
    }
}
