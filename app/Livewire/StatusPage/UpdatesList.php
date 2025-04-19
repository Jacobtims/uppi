<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPage;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class UpdatesList extends Component
{
    public StatusPage $statusPage;

    public function mount(StatusPage $statusPage)
    {
        $this->statusPage = $statusPage;
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="my-8">
            <h2 class="font-medium text-neutral-900 text-lg mb-4 pl-1">System Updates</h2>
            
            <div class="relative">
                <div class="absolute left-4 sm:left-[16px] top-3 -translate-x-1/2 w-[3px] h-[calc(100%-32px)] bg-neutral-200 rounded-full"></div>

                <div class="space-y-6">
                    <div class="relative animate-pulse">
                        <div class="absolute left-4 sm:left-[16px] top-3 -translate-x-1/2 w-[10px] h-[10px] rounded-full border-[2px] border-white ring-[2px] bg-neutral-200 z-10 shadow-sm"></div>
                        
                        <div class="ml-10 sm:ml-14 bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs bg-neutral-200 w-20 h-6"></div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs bg-neutral-200 w-24 h-6"></div>
                                <div class="sm:ml-auto w-full sm:w-auto text-right mt-2 sm:mt-0">
                                    <div class="inline-block bg-neutral-200 rounded w-24 h-5"></div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <div class="h-6 bg-neutral-200 rounded-lg w-3/4"></div>
                                <div class="h-4 bg-neutral-200 rounded w-full"></div>
                                <div class="h-4 bg-neutral-200 rounded w-2/3"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative animate-pulse">
                        <div class="absolute left-4 sm:left-[16px] top-3 -translate-x-1/2 w-[10px] h-[10px] rounded-full border-[2px] border-white ring-[2px] bg-neutral-200 z-10 shadow-sm"></div>
                        
                        <div class="ml-10 sm:ml-14 bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs bg-neutral-200 w-20 h-6"></div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs bg-neutral-200 w-16 h-6"></div>
                                <div class="sm:ml-auto w-full sm:w-auto text-right mt-2 sm:mt-0">
                                    <div class="inline-block bg-neutral-200 rounded w-24 h-5"></div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <div class="h-6 bg-neutral-200 rounded-lg w-1/2"></div>
                                <div class="h-4 bg-neutral-200 rounded w-3/4"></div>
                                <div class="h-4 bg-neutral-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $updates = $this->statusPage->updates()
            ->where('is_published', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.status-page.updates-list', [
            'updates' => $updates,
        ]);
    }
} 