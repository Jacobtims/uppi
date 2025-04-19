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
        <div class="animate-pulse bg-white rounded-2xl shadow-sm border border-neutral-100 p-7">
            <div class="flex flex-col items-center py-8">
                <div class="bg-neutral-200 rounded-full p-5 mb-5 h-24 w-24 flex items-center justify-center">
                    <div class="h-14 w-14 rounded-full bg-neutral-300"></div>
                </div>
                <div class="h-8 bg-neutral-200 rounded-lg w-48 mb-2"></div>
                <div class="h-5 bg-neutral-200 rounded-lg w-64"></div>
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
