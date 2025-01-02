<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPage;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class OverallStatus extends Component
{
    public StatusPage $statusPage;

    public function mount(StatusPage $statusPage)
    {
        $this->statusPage = $statusPage->load(['items' => function ($query) {
            $query->where('is_enabled', true)
                ->with(['monitor' => function ($query) {
                    $query->where('is_enabled', true)
                        ->select('id', 'status');
                }]);
        }]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="animate-pulse">
            <div class="p-4 rounded-lg bg-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 rounded-full bg-gray-300"></div>
                    <div class="h-6 bg-gray-300 rounded w-32"></div>
                </div>
                <div class="mt-2">
                    <div class="h-4 bg-gray-300 rounded w-48"></div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $isOperational = Cache::remember(
            "status_page_operational_{$this->statusPage->id}",
            now()->addMinutes(1),
            fn () => $this->statusPage->isOk()
        );

        return view('livewire.status-page.overall-status', [
            'isOperational' => $isOperational,
        ]);
    }
}
