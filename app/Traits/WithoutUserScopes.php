<?php

namespace App\Traits;

trait WithoutUserScopes
{
    protected function applyGlobalScopes($query)
    {
        return $query->withoutGlobalScope('user');
    }

    public static function getEloquentQuery()
    {
        return parent::getEloquentQuery()->tap(fn($query) => (new static)->applyGlobalScopes($query));
    }
}
