<?php

namespace App\Notifications;

use App\Models\Anomaly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;
use NotificationChannels\Bird\BirdMessage;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Messagebird\MessagebirdMessage;
use NotificationChannels\Pushover\PushoverMessage;

class MonitorDownNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->error()
            ->subject("🔴 Monitor Down: {$monitor->name}")
            ->greeting("The monitor {$monitor->name} is currently DOWN.")
            ->line("Target: {$monitor->address}")
            ->line("Down since: {$this->anomaly->started_at->format('Y-m-d H:i:s')}")
            ->line("Last check output: {$this->anomaly->checks->last()?->output}")
            ->action('Open '.config('app.name'), url('/'))
            ->line('Thank you for using '.config('app.name').'!');
    }

    public function toMessagebird($notifiable): MessagebirdMessage
    {
        return new MessagebirdMessage("🔴 Monitor DOWN: {$this->anomaly->monitor->name} ({$this->anomaly->monitor->address})");
    }

    public function toBird(object $notifiable): BirdMessage
    {
        return new BirdMessage("🔴 Monitor DOWN: {$this->anomaly->monitor->name} ({$this->anomaly->monitor->address})");
    }

    public function toPushover(object $notifiable): PushoverMessage
    {
        return PushoverMessage::create()
            ->title("🔴 Monitor DOWN: {$this->anomaly->monitor->name} ({$this->anomaly->monitor->address})")
            ->content("The monitor {$this->anomaly->monitor->name} is down and not responding. Last check output: {$this->anomaly->checks->last()?->output}")
            ->emergencyPriority(60, 360)
            ->url(url('/'), 'Open '.config('app.name'));
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $monitor = $this->anomaly->monitor;
        $lastCheck = $this->anomaly->checks->last();

        return ExpoMessage::create()
            ->title("🔴 Monitor Down: {$monitor->name}")
            ->body("The monitor {$monitor->name} is down and not responding. Last check output: {$lastCheck?->output}")
            ->ttl(3600)
            ->priority('high');
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        $monitor = $this->anomaly->monitor;
        $lastCheck = $this->anomaly->checks->last();

        $template = <<<JSON
        {
            "blocks": [
                {
                    "type": "header",
                    "text": {
                        "type": "plain_text",
                        "text": "🔴 Monitor Down: {$monitor->name}",
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
                            "text": "*Down Since:*\\n{$this->anomaly->started_at->format('Y-m-d H:i:s')}"
                        },
                        {
                            "type": "mrkdwn",
                            "text": "*Response Time:*\\n{$lastCheck?->response_time}ms"
                        }
                    ]
                }
            ]
        }
        JSON;

        return (new SlackMessage)->usingBlockKitTemplate($template);
    }
}
