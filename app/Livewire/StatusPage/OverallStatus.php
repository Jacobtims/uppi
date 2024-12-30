<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPage;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class OverallStatus extends Component
{
    public StatusPage $statusPage;

    public function mount(StatusPage $statusPage)
    {
        $this->statusPage = $statusPage->load(['items' => function($query) {
            $query->where('is_enabled', true)
                ->with(['monitor' => function($query) {
                    $query->where('is_enabled', true)
                        ->select('id', 'status');
                }]);
        }]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
        HTML;
    }

    public function render()
    {
        $isOperational = Cache::remember(
            "status_page_operational_{$this->statusPage->id}",
            now()->addMinutes(1),
            fn() => $this->statusPage->isOk()
        );

        return view('livewire.status-page.overall-status', [
            'isOperational' => $isOperational,
        ]);
    }
}
