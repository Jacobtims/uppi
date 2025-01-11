<?php

namespace App\Observers;

use App\Models\Update;

class UpdateObserver
{
    public function creating(Update $update): void
    {
        $update->slug = $update->slug ?? str($update->title)->slug();
    }
}
