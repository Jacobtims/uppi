<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPageItem;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class MonitorStatus extends Component
{
    public StatusPageItem $item;

    public function mount(StatusPageItem $item)
    {
        $this->item = $item->load(['monitor' => function($query) {
            $query->with(['checks' => function($query) {
                $query->where('checked_at', '>=', now()->subDays(30))
                    ->orderBy('checked_at');
            }, 'anomalies' => function($query) {
                $query->where('started_at', '>=', now()->subDays(30));
            }]);
        }]);
    }

    public function placeholder()
    {
        $boxes = str_repeat('<div class="w-5 h-6 bg-gray-200 rounded"></div>', 30);

        return '<div class="animate-pulse">
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <div class="h-5 bg-gray-200 rounded w-32"></div>
                    <div class="h-5 bg-gray-200 rounded w-24"></div>
                </div>
                <div class="flex space-x-1">' . $boxes . '</div>
            </div>
        </div>';
    }

    protected function generateBoxesHtml(): string
    {
        $boxes = [];
        for ($i = 0; $i < 30; $i++) {
            $boxes[] = '<div class="w-5 h-6 bg-gray-200 rounded"></div>';
        }
        return implode("\n", $boxes);
    }

    public function render()
    {
        $status30Days = Cache::remember(
            "monitor_status_{$this->item->monitor_id}",
            now()->addMinutes(5),
            fn() => $this->item->monitor->status30Days()
        );

        return view('livewire.status-page.monitor-status', [
            'dates' => collect($status30Days)->keys(),
            'statuses' => collect($status30Days)->values(),
        ]);
    }
}
