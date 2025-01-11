<?php

namespace App\Jobs;

use App\Enums\StatusPage\UpdateStatus;
use App\Enums\StatusPage\UpdateType;
use App\Models\Anomaly;
use App\Models\Update;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateMonitorUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Anomaly $anomaly
    ) {}

    public function handle(): void
    {
        $monitor = $this->anomaly->monitor;

        // Skip if auto-update is disabled
        if (!$monitor->auto_create_update) {
            return;
        }

        // Get all status pages where this monitor is active
        $statusPages = $monitor->statusPageItems()
            ->where('is_enabled', true)
            ->with('statusPage')
            ->get()
            ->pluck('statusPage');

        if ($statusPages->isEmpty()) {
            return;
        }

        // Create the update
        $update = Update::create([
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'type' => UpdateType::ANOMALY,
            'status' => UpdateStatus::UNDER_INVESTIGATION,
            'is_published' => true,
            'slug' => $this->generateUniqueSlug(),
            'user_id' => $monitor->user_id,
            'from' => $this->anomaly->started_at,
        ]);

        // Associate with monitor
        $update->monitors()->attach($monitor->id);

        // Associate with status pages
        $update->statusPages()->attach($statusPages->pluck('id'));
    }

    protected function getTitle(): string
    {
        $monitor = $this->anomaly->monitor;
        $template = $monitor->update_values['title'] ?? ':monitor_name is experiencing issues';

        return $this->replaceVariables($template);
    }

    protected function getContent(): string
    {
        $monitor = $this->anomaly->monitor;
        $template = $monitor->update_values['content'] ?? "Our automated monitoring & alerting system has detected that :monitor_name is experiencing issues. Because of these issues, we've created this update to keep you informed.\n\nOur team has been notified and is investigating. We apologize for the inconvenience.";

        return $this->replaceVariables($template);
    }

    protected function generateUniqueSlug(): string
    {
        $baseSlug = Str::slug($this->getTitle());
        $slug = $baseSlug;
        $counter = 1;

        while (Update::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    protected function replaceVariables(string $template): string
    {
        $monitor = $this->anomaly->monitor;
        $variables = [
            ':monitor_name' => $monitor->name,
            ':monitor_address' => $monitor->address,
            ':monitor_type' => $monitor->type->value,
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }
} 