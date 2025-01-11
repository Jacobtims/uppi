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
        <div class="animate-pulse space-y-4">
            <div class="bg-white shadow rounded-lg border border-neutral-100 p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 rounded-full bg-gray-200"></div>
                    <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg border border-neutral-100 p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 rounded-full bg-gray-200"></div>
                    <div class="h-6 bg-gray-200 rounded w-1/4"></div>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
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