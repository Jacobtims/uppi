<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPage;
use Livewire\Component;

class MonitorsList extends Component
{
    public StatusPage $statusPage;

    public function mount(StatusPage $statusPage)
    {
        $this->statusPage = $statusPage->load(['items' => function($query) {
            $query->where('is_enabled', true)
                ->orderBy('order')
                ->with(['monitor' => function($query) {
                    $query->where('is_enabled', true);
                }]);
        }]);
    }

    public function render()
    {
        return view('livewire.status-page.monitors-list');
    }
}
