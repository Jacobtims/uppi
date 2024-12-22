<?php

namespace App\Observers;

class UserIdObserver
{
    public function creating($model): void
    {
        if (empty($model->user_id)) {
            $model->user_id = auth()->id();
        }
    }
}
