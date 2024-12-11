<?php

namespace App\Notifications;

use App\Models\Anomaly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class MonitorRecoveredNotification extends Notification implements ShouldQueue
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
        $duration = $this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true);

        return (new MailMessage)
            ->success()
            ->subject("✅ Monitor Recovered: {$monitor->name}")
            ->line("The monitor {$monitor->name} is back UP!")
            ->line("Target: {$monitor->target}")
            ->line("Downtime duration: {$duration}")
            ->line("Recovered at: {$this->anomaly->ended_at->format('Y-m-d H:i:s')}")
            ->action('View Monitor', url("/monitors/{$monitor->id}"));
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        $monitor = $this->anomaly->monitor;
        $duration = $this->anomaly->started_at->diffForHumans($this->anomaly->ended_at, true);

        return (new SlackMessage)
            ->success()
            ->content("✅ Monitor Recovered: {$monitor->name}")
            ->attachment(function ($attachment) use ($monitor, $duration) {
                $attachment
                    ->title($monitor->name)
                    ->fields([
                        'Target' => $monitor->target,
                        'Downtime Duration' => $duration,
                        'Recovered At' => $this->anomaly->ended_at->format('Y-m-d H:i:s'),
                    ]);
            });
    }
}
