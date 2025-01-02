<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait WithoutUserScopes
{
    protected function applyGlobalScopes($query): Builder
    {
        return $query->withoutGlobalScope('user')
            ->withoutGlobalScope('userMonitors');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->tap(fn ($query) => (new static)->applyGlobalScopes($query));
    }
}
