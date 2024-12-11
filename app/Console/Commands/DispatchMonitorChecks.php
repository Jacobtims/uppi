<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use Illuminate\Console\Command;

class DispatchMonitorChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitors:check {--monitor-id= : ID of a specific monitor to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch check jobs for monitors that are due';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $query = Monitor::query()
            ->where('is_enabled', true)
            ->where(function ($query) {
                $query->whereNull('last_checked_at')
                    ->orWhereRaw('DATE_ADD(last_checked_at, INTERVAL frequency SECOND) <= NOW()');
            });

        // If monitor ID is provided, only check that specific monitor
        if ($monitorId = $this->option('monitor-id')) {
            $query->where('id', $monitorId);
        }

        $monitors = $query->get();

        $count = 0;
        foreach ($monitors as $monitor) {
            dispatch($monitor->makeCheckJob());
            $count++;

            // Update last_checked_at timestamp
            $monitor->last_checked_at = now();
            $monitor->save();
        }

        $this->info("Dispatched checks for {$count} monitor(s).");
    }
}
