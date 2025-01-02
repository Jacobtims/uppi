<?php

namespace App\CacheTasks;

use App\Models\Check;
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

    public static function getTtl(): int
    {
        return 60;
    }

    public function execute(): Collection
    {
        if ($this->userId === null) {
            throw new \RuntimeException('Cache task must be scoped to a user');
        }

        $today = now()->startOfDay();
        $start = $today->copy()->subDays($this->days);
        $end = $today->copy()->subDay()->endOfDay();

        // Get enabled items
        $query = StatusPageItem::query()
            ->where('status_page_id', $this->statusPageId)
            ->where('is_enabled', true)
            ->whereHas('statusPage', function ($query) {
                $query->where('user_id', $this->userId)
                    ->where('is_enabled', true);
            })
            ->whereHas('monitor', function ($query) {
                $query->where('is_enabled', true);
            });

        if ($this->id !== null) {
            $query->where('id', $this->id);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return collect();
        }

        $monitorIds = $items->pluck('monitor_id');

        // Get all checks grouped by date and monitor
        $checks = Check::selectRaw('monitor_id, DATE(checked_at) as date')
            ->whereIn('monitor_id', $monitorIds)
            ->whereBetween('checked_at', [$start, $end])
            ->groupBy('monitor_id', 'date')
            ->get()
            ->groupBy('monitor_id')
            ->map(fn ($checks) => $checks->pluck('date'));

        // Get all anomalies grouped by date and monitor
        $anomalies = StatusPageItem::with(['monitor.anomalies' => function ($query) use ($start, $end) {
                $query->whereBetween('started_at', [$start, $end])
                    ->whereNull('ended_at')
                    ->selectRaw('monitor_id, DATE(started_at) as date');
            }])
            ->whereIn('id', $items->pluck('id'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->monitor_id => $item->monitor->anomalies->pluck('date')];
            });

        $result = collect();
        foreach ($items as $item) {
            $status = collect();
            $monitorChecks = $checks->get($item->monitor_id, collect());
            $monitorAnomalies = $anomalies->get($item->monitor_id, collect());

            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                $dateString = $date->toDateString();

                if (!$monitorChecks->contains($dateString)) {
                    $status[$dateString] = null;
                    continue;
                }

                $status[$dateString] = !$monitorAnomalies->contains($dateString);
            }

            $result[$item->id] = $status;
        }

        return $result;
    }
}
