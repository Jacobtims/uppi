<?php

namespace App\Jobs;

use App\CacheTasks\CacheTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshCacheTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $taskClass,
        private readonly string $userId,
        private readonly array $parameters = []
    ) {}

    public function handle(): void
    {
        /** @var CacheTask $task */
        $task = new $this->taskClass(...$this->parameters);
        $task->forUser($this->userId)->refresh();
    }
}
