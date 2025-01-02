<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPageItem;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class MonitorStatus extends Component
{
    public StatusPageItem $item;

    public function mount(StatusPageItem $item)
    {
        $this->item = $item->load(['monitor' => function ($query) {
            $query->with(['checks' => function ($query) {
                $query->where('checked_at', '>=', now()->subDays(30))
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
        $status30Days = Cache::remember(
            "monitor_status_{$this->item->monitor_id}",
            now()->addMinutes(5),
            fn () => $this->item->monitor->status30Days()
        );

        return view('livewire.status-page.monitor-status', [
            'dates' => collect($status30Days)->keys(),
            'statuses' => collect($status30Days)->values(),
        ]);
    }
}
