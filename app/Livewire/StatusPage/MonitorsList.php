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
        <div>
            <h2 class="font-medium text-neutral-900 text-lg mb-4 pl-1">Service Status</h2>
            <div class="flex flex-col gap-4">
                <div class="animate-pulse bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex flex-col space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-6 w-6 bg-neutral-200 rounded-md"></div>
                                <div class="h-5 bg-neutral-200 rounded-lg w-36"></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-neutral-200"></div>
                                <div class="h-5 bg-neutral-200 rounded-lg w-16"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-30 gap-2">
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                        </div>
                    </div>
                </div>
                
                <div class="animate-pulse bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex flex-col space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-6 w-6 bg-neutral-200 rounded-md"></div>
                                <div class="h-5 bg-neutral-200 rounded-lg w-28"></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-neutral-200"></div>
                                <div class="h-5 bg-neutral-200 rounded-lg w-16"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-30 gap-2">
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                            <div class="h-8 rounded-lg border border-neutral-200 bg-neutral-200"></div>
                        </div>
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
