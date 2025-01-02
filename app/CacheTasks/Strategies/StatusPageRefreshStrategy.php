<?php

namespace App\CacheTasks\Strategies;

use App\CacheTasks\RefreshStrategy;
use App\Models\StatusPage;
use Illuminate\Support\Collection;

class StatusPageRefreshStrategy implements RefreshStrategy
{
    public function __construct(
        private readonly string $taskClass
    ) {}

    public function getConstructorParameters(string $userId): Collection
    {
        return StatusPage::where('user_id', $userId)
            ->where('is_enabled', true)
            ->select('id')
            ->get()
            ->map(fn (StatusPage $page) => [$this->taskClass, $userId, [$page->id]]);
    }
}
