<?php

namespace App\Console\Commands;

use App\CacheTasks\CacheTaskRegistry;
use App\Models\User;
use Illuminate\Console\Command;

class RefreshCacheTasksCommand extends Command
{
    protected $signature = 'cache:refresh-tasks';
    protected $description = 'Dispatch jobs to refresh all cache tasks that are due for refresh';

    public function __construct(
        private readonly CacheTaskRegistry $registry
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        User::query()
            ->select('id')
            ->each(function (User $user) {
                $this->registry->getTasks()
                    ->each(function (string $taskClass) use ($user) {
                        /** @var \App\CacheTasks\CacheTask $task */
                        $task = new $taskClass();

                        if ($task->ttl() <= 0) {
                            return;
                        }

                        $this->info("Dispatching refresh jobs for: " . class_basename($taskClass) . " (User: {$user->id})");
                        $taskClass::refreshForUser($user->id);
                    });
            });
    }
}
