<?php

namespace App\Livewire;

use Livewire\Component;

class GlobalStatusBadge extends Component
{
    public function render()
    {
        $isOk = auth()->user()->isOk();

        return view('livewire.global-status-badge', [
            'isOk' => $isOk,
        ]);
    }
}
