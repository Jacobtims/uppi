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
    protected $signature = 'monitors:check {--monitor-id= : ID of a specific monitor to check} {--force : Force the check to run even if the monitor is not due}';

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
            ->where('is_enabled', true);

        if (!$this->option('force')) {
            $query = $query->where(function ($query) {
                $query->whereNull('last_checked_at')
                    ->orWhereRaw('last_checked_at <= datetime(?, -interval || ? )', [now(), ' minutes']);
            });
        }

        if ($monitorId = $this->option('monitor-id')) {
            $query->where('id', $monitorId);
        }

        $monitors = $query->get();

        foreach ($monitors as $monitor) {
            dispatch($monitor->makeCheckJob());
            $monitor->update(['last_checked_at' => now()]);
        }

        $this->info("Dispatched checks for {$monitors->count()} monitor(s).");
    }
}
