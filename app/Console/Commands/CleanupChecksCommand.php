<?php

namespace App\Console\Commands;

use App\Enums\Checks\Status;
use App\Models\Check;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupChecksCommand extends Command
{
    protected $signature = 'checks:cleanup';

    protected $description = 'Clean up old checks, keeping only the last successful check per day when all checks were OK';

    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(7);

        // Get all dates before cutoff that have checks
        $dates = Check::query()
            ->select(DB::raw('DATE(checked_at) as date'))
            ->where('checked_at', '<', $cutoffDate)
            ->groupBy('date')
            ->get()
            ->pluck('date');

        $totalDeleted = 0;

        foreach ($dates as $date) {
            // For each date, group by monitor
            $monitors = Check::query()
                ->select('monitor_id')
                ->whereDate('checked_at', $date)
                ->groupBy('monitor_id')
                ->get();

            foreach ($monitors as $monitor) {
                // Get all checks for this monitor on this date
                $checksForDay = Check::query()
                    ->where('monitor_id', $monitor->monitor_id)
                    ->whereDate('checked_at', $date)
                    ->get();

                // If all checks were OK, delete all but the last one
                if ($checksForDay->every(fn ($check) => $check->status === Status::OK)) {
                    $lastCheck = $checksForDay->sortByDesc('checked_at')->first();

                    $deleted = Check::query()
                        ->where('monitor_id', $monitor->monitor_id)
                        ->whereDate('checked_at', $date)
                        ->where('id', '!=', $lastCheck->id)
                        ->delete();

                    $totalDeleted += $deleted;
                }
            }
        }

        $this->info("Deleted {$totalDeleted} checks.");
    }
}
