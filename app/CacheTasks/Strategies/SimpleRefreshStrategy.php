<?php

namespace App\CacheTasks\Strategies;

use App\CacheTasks\RefreshStrategy;
use App\Jobs\RefreshCacheTaskJob;
use Illuminate\Support\Collection;

class SimpleRefreshStrategy implements RefreshStrategy
{
    public function __construct(
        private readonly string $taskClass
    ) {}

    public function getRefreshJobs(string $userId): Collection
    {
        return collect([
            new RefreshCacheTaskJob($this->taskClass, $userId)
        ]);
    }
}
