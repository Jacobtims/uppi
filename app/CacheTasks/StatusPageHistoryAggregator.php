<?php

namespace App\CacheTasks;

use App\Models\Check;
use App\Models\Monitor;
use App\Models\StatusPage;
use App\Models\StatusPageItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatusPageHistoryAggregator extends CacheTask
{
    public function __construct(
        private readonly string $statusPageId,
        private readonly int $days = 30
    ) {}

    public function key(): string
    {
        return "status_page_history_{$this->statusPageId}_{$this->days}";
    }

    public function ttl(): int
    {
        return 60; // Cache for 1 hour
    }

    public static function getStatusPagesForUser(string $userId): Collection
    {
        return StatusPage::where('user_id', $userId)
            ->where('is_enabled', true)
            ->select('id')
            ->get();
    }

    public function execute(): Collection
    {
        if ($this->userId === null) {
            throw new \RuntimeException('Cache task must be scoped to a user');
        }

        $today = now()->startOfDay();
        $start = $today->copy()->subDays($this->days);
        $end = $today->copy()->subDay()->endOfDay(); // Yesterday end of day

        // Get the status page with its items
        $page = StatusPage::where('id', $this->statusPageId)
            ->where('user_id', $this->userId)
            ->where('is_enabled', true)
            ->with(['items' => function ($query) {
                $query->where('is_enabled', true)
                    ->whereHas('monitor', function ($query) {
                        $query->where('is_enabled', true);
                    })
                    ->with(['monitor']);
            }])
            ->firstOrFail();

        $result = collect();

        foreach ($page->items as $item) {
            // Get all anomalies in the date range
            $anomalies = $item->monitor->anomalies()
                ->where('started_at', '>=', $start)
                ->where('started_at', '<=', $end)
                ->get();

            // Get all days where we had checks
            $checks = $item->monitor->checks()
                ->where('checked_at', '>=', $start)
                ->where('checked_at', '<=', $end)
                ->get();

            // Build the array with dates as keys
            $status = collect();
            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                $dateString = $date->toDateString(); // Format: YYYY-MM-DD

                // Get checks for this day
                $dayChecks = $checks->filter(function ($check) use ($date) {
                    return $check->checked_at->startOfDay()->equalTo($date);
                });

                // Get anomalies for this day
                $dayAnomalies = $anomalies->filter(function ($anomaly) use ($date) {
                    return $anomaly->started_at->startOfDay()->equalTo($date);
                });

                if ($dayChecks->isEmpty()) {
                    // No checks for this day
                    $status[$dateString] = null;
                } else {
                    // If we have anomalies for this day, mark as down
                    $status[$dateString] = $dayAnomalies->isEmpty();
                }
            }

            $result[$item->id] = $status;
        }

        return $result;
    }

    public static function refreshForUser(string $userId): void
    {
        StatusPage::where('user_id', $userId)
            ->where('is_enabled', true)
            ->select('id')
            ->each(function ($page) use ($userId) {
                dispatch(new \App\Jobs\RefreshCacheTaskJob(static::class, $userId, [$page->id]))->onQueue('cache');
            });
    }
}
