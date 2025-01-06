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
        $monitors = Monitor::query()
            ->where('is_enabled', true)
            ->when(!$this->option('force'), function ($query) {
                $query->where('next_check_at', '<=', now());
            })
            ->when($this->option('monitor-id'), function ($query, $monitorId) {
                $query->where('id', $monitorId);
            })
            ->withoutGlobalScope('user')
            ->get();

        $count = 0;
        foreach ($monitors as $monitor) {
            dispatch($monitor->makeCheckJob())->onQueue('checks');
            $monitor->updateNextCheck();
            $count++;
        }

        $this->info("Dispatched checks for {$count} monitor(s).");
    }
}
