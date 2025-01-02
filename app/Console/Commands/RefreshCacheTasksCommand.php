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
                        // Create a test instance to check TTL
                        /** @var \App\CacheTasks\CacheTask $task */
                        $task = new $taskClass();

                        // Check if task needs refresh
                        $ttl = $task->ttl();
                        if ($ttl <= 0) {
                            return;
                        }

                        // Get refresh jobs from the task's strategy
                        $task->getRefreshStrategy()
                            ->getRefreshJobs($user->id)
                            ->each(function ($job) use ($taskClass, $user) {
                                $this->info("Dispatching refresh job for: " . class_basename($taskClass) . " (User: {$user->id})");
                                $job->onQueue('cache')->dispatch();
                            });
                    });
            });
    }
}
