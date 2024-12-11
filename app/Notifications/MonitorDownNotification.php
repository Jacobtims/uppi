<?php

namespace App\Notifications;

use App\Models\Anomaly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Notifications\Notification;

class MonitorDownNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Anomaly $anomaly
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'slack'];
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
            ->action('Open ' . config('app.name'), url("/"))
            ->line('Thank you for using ' . config('app.name') . '!');
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
