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
    private readonly int $days;

    public function __construct(int $days = 30)
    {
        $this->days = $days;
    }

    public function key(): string
    {
        return "status_page_history_{$this->days}";
    }

    public function ttl(): int
    {
        return 60; // Cache for 1 hour
    }

    public function execute(): Collection
    {
        if ($this->userId === null) {
            throw new \RuntimeException('Cache task must be scoped to a user');
        }

        $today = now()->startOfDay();
        $start = $today->copy()->subDays($this->days);
        $end = $today->copy()->subDay()->endOfDay(); // Yesterday end of day

        // If we have a specific status page ID, only get that one
        $query = StatusPage::where('user_id', $this->userId)
            ->where('is_enabled', true);

        if ($this->id !== null) {
            $query->where('id', $this->id);
        }

        // Get status pages with their items
        $pages = $query->with(['items' => function ($query) {
                $query->where('is_enabled', true)
                    ->whereHas('monitor', function ($query) {
                        $query->where('is_enabled', true);
                    })
                    ->with(['monitor']);
            }])
            ->get();

        $result = collect();

        foreach ($pages as $page) {
            foreach ($page->items as $item) {
                // Get all anomalies in the date range
                $anomalies = $item->monitor->anomalies()
                    ->where('started_at', '>=', $start)
                    ->where('started_at', '<=', $end)
                    ->get()
                    ->map(function ($anomaly) {
                        return [
                            'date' => Carbon::parse($anomaly->started_at)->startOfDay(),
                            'had_downtime' => true,
                        ];
                    });

                // Get all days where we had checks
                $checks = $item->monitor->checks()
                    ->where('checked_at', '>=', $start)
                    ->where('checked_at', '<=', $end)
                    ->get()
                    ->groupBy(function ($check) {
                        return Carbon::parse($check->checked_at)->startOfDay()->toDateString();
                    })
                    ->map(function ($dayChecks) {
                        return [
                            'date' => Carbon::parse($dayChecks->first()->checked_at)->startOfDay(),
                            'had_downtime' => false,
                        ];
                    });

                // Merge anomalies and checks
                $allDays = $anomalies->concat($checks)
                    ->groupBy(function ($item) {
                        return $item['date']->toDateString();
                    });

                // Build the array with dates as keys
                $status = collect();
                for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                    $dateString = $date->toDateString();

                    if (!isset($allDays[$dateString])) {
                        // No data for this day
                        $status[$dateString] = null;
                    } else {
                        // If any record for this day had downtime, mark as false (down)
                        $status[$dateString] = !$allDays[$dateString]->contains('had_downtime', true);
                    }
                }

                $result[$item->id] = $status;
            }
        }

        return $result;
    }
}
