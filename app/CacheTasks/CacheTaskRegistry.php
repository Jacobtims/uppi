<?php

namespace App\CacheTasks;

use Illuminate\Support\Collection;

class CacheTaskRegistry
{
    private const TASKS = [
        ResponseTimeAggregator::class,
        StatusPageHistoryAggregator::class,
    ];

    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = collect(self::TASKS);
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }
}
