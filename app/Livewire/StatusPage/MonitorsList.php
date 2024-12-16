<?php

namespace App\Livewire\StatusPage;

use App\Models\StatusPage;
use Livewire\Component;

class MonitorsList extends Component
{
    public StatusPage $statusPage;

    public function mount(StatusPage $statusPage)
    {
        $this->statusPage = $statusPage;
    }

    public function render()
    {
        return view('livewire.status-page.monitors-list');
    }
}
