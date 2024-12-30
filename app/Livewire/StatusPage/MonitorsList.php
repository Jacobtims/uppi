<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPage;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
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

    public function placeholder()
    {
        return <<<'HTML'
        <div class="animate-pulse">
            <div class="space-y-4">
                <div class="h-12 bg-gray-200 rounded"></div>
                <div class="h-12 bg-gray-200 rounded"></div>
                <div class="h-12 bg-gray-200 rounded"></div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.status-page.monitors-list');
    }
}
