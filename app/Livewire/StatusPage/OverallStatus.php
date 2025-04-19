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
        <div class="animate-pulse bg-white rounded-xl shadow-sm border border-neutral-100 p-7 relative overflow-hidden">
            <!-- Subtle pattern background -->
            <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#9ca3af_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="absolute right-0 top-0 h-20 w-40 bg-gradient-to-bl from-neutral-100 to-transparent rounded-bl-[100px]"></div>

            <div class="flex flex-col items-center py-8 relative z-10">
                <div class="bg-neutral-200 rounded-full p-5 mb-5 shadow-inner flex items-center justify-center">
                    <div class="h-14 w-14 rounded-full bg-neutral-300"></div>
                </div>
                <div class="px-4 py-2 bg-neutral-100 rounded-full w-48 h-8 mb-4"></div>
                <div class="h-6 bg-neutral-200 rounded-lg w-64 mb-3"></div>
                <div class="h-5 bg-neutral-200 rounded-lg w-72"></div>
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
