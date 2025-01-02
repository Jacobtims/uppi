<?php

namespace App\CacheTasks;

use App\CacheTasks\Strategies\SimpleRefreshStrategy;
use Illuminate\Support\Facades\Cache;

abstract class CacheTask
{
    protected ?string $userId = null;
    protected ?string $id = null;

    /**
     * Get the cache key for this task
     */
    abstract public function key(): string;

    /**
     * Get the cache TTL in minutes
     */
    abstract public function ttl(): int;

    /**
     * Execute the task and return the data to cache
     */
    abstract public function execute(): mixed;

    /**
     * Get the refresh strategy for this task
     */
    public function getRefreshStrategy(): RefreshStrategy
    {
        return new SimpleRefreshStrategy(static::class);
    }

    public function forUser(string $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    public function forId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the full cache key including user scope
     */
    protected function getCacheKey(): string
    {
        if ($this->userId === null) {
            throw new \RuntimeException('Cache task must be scoped to a user');
        }

        return "user_{$this->userId}_{$this->key()}";
    }
    public function get(): mixed
    {
        $data = Cache::remember(
            $this->getCacheKey(),
            $this->ttl() * 60,
            fn () => $this->execute()
        );

        if ($this->id !== null && is_array($data)) {
            return $data[$this->id] ?? null;
        }

        return $data;
    }

    public function refresh(): void
    {
        Cache::put(
            $this->getCacheKey(),
            $this->execute(),
            $this->ttl() * 60
        );
    }
}
