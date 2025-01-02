<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPage;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class MonitorsList extends Component
{
    public StatusPage $statusPage;

    public function mount(StatusPage $statusPage)
    {
        $this->statusPage = $statusPage->load(['items' => function ($query) {
            $query->where('is_enabled', true)
                ->orderBy('order')
                ->with(['monitor' => function ($query) {
                    $query->where('is_enabled', true);
                }]);
        }]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="animate-pulse space-y-4">
            <div class="bg-gray-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                        <div class="h-5 bg-gray-300 rounded w-1/3"></div>
                    </div>
                    <div class="flex space-x-2">
                        <div class="w-16 h-5 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                        <div class="h-5 bg-gray-300 rounded w-1/4"></div>
                    </div>
                    <div class="flex space-x-2">
                        <div class="w-16 h-5 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                        <div class="h-5 bg-gray-300 rounded w-2/5"></div>
                    </div>
                    <div class="flex space-x-2">
                        <div class="w-16 h-5 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.status-page.monitors-list');
    }
}
