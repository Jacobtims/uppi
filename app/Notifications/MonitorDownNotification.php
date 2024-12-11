<?php

namespace App\Notifications;

use App\Models\Anomaly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
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
            ->line("The monitor {$monitor->name} is currently DOWN.")
            ->line("Target: {$monitor->target}")
            ->line("Last check output: {$this->anomaly->checks->last()?->output}")
            ->line("Down since: {$this->anomaly->started_at->format('Y-m-d H:i:s')}")
            ->action('View Monitor', url("/monitors/{$monitor->id}"));
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        $monitor = $this->anomaly->monitor;

        return (new SlackMessage)
            ->error()
            ->content("🔴 Monitor Down: {$monitor->name}")
            ->attachment(function ($attachment) use ($monitor) {
                $attachment
                    ->title($monitor->name)
                    ->fields([
                        'Target' => $monitor->target,
                        'Last Output' => $this->anomaly->checks->last()?->output,
                        'Down Since' => $this->anomaly->started_at->format('Y-m-d H:i:s'),
                    ]);
            });
    }
}
