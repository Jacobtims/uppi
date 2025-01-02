<?php

namespace App\Livewire\StatusPage;

use App\CacheTasks\StatusPageHistoryAggregator;
use App\Models\StatusPageItem;
use Illuminate\Support\Collection;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Carbon\Carbon;

#[Lazy]
class MonitorStatus extends Component
{
    public StatusPageItem $item;

    public function mount(StatusPageItem $item)
    {
        $this->item = $item->load(['monitor' => function ($query) {
            $query->with(['checks' => function ($query) {
                $query->where('checked_at', '>=', now()->startOfDay())
                    ->orderBy('checked_at');
            }, 'anomalies' => function ($query) {
                $query->where('started_at', '>=', now()->subDays(30));
            }]);
        }]);
    }

    public function placeholder()
    {
        return view('livewire.status-page.monitor-status-placeholder');
    }

    public function render()
    {
        // Get historical data from cache
        $historicalStatus = (new StatusPageHistoryAggregator())
            ->forUser($this->item->monitor->user_id)
            ->get()
            ->get($this->item->id, collect());

        // Get today's data in real-time
        $todayChecks = $this->item->monitor->checks()
            ->where('checked_at', '>=', now()->startOfDay())
            ->get();

        $todayAnomalies = $this->item->monitor->anomalies()
            ->where('started_at', '>=', now()->startOfDay())
            ->exists();

        $todayStatus = [];
        if ($todayChecks->isNotEmpty()) {
            $todayStatus[now()->format('Y-m-d')] = !$todayAnomalies;
        }

        // Merge historical and today's data
        $status30Days = $historicalStatus->merge($todayStatus);

        // Sort by date and format for display
        $status30Days = $status30Days
            ->sortKeys()
            ->mapWithKeys(function ($status, $date) {
                return [Carbon::parse($date)->format('M j') => $status];
            });

        return view('livewire.status-page.monitor-status', [
            'dates' => $status30Days->keys(),
            'statuses' => $status30Days->values(),
        ]);
    }
}
