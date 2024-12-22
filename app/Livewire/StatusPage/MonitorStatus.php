<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPageItem;
use Livewire\Component;

class MonitorStatus extends Component
{
    public StatusPageItem $item;

    public function mount(StatusPageItem $item)
    {
        $this->item = $item;
    }

    public function render()
    {
        $dates = collect($this->item?->monitor->status30Days())->keys();
        $statuses = collect($this->item?->monitor->status30Days())->values();

        return view('livewire.status-page.monitor-status', [
            'dates' => $dates,
            'statuses' => $statuses,
        ]);
    }
}
