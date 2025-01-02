<?php

namespace App\CacheTasks\Strategies;

use App\CacheTasks\RefreshStrategy;
use Illuminate\Support\Collection;

class SimpleRefreshStrategy implements RefreshStrategy
{
    public function __construct(
        private readonly string $taskClass
    ) {}

    public function getConstructorParameters(string $userId): Collection
    {
        return collect([[$this->taskClass, $userId]]);
    }
}
