<?php

namespace App\Observers;

use App\Models\Update;
use Illuminate\Support\Str;

class UpdateObserver
{
    public function creating(Update $model): void
    {
        $model->slug = $model->slug ?? now()->format('YmdHis') . '-' . str($model->title)->slug() . '-' . Str::random(6);
    }
}
