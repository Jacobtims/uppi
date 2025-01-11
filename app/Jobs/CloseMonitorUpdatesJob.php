<?php

namespace App\Jobs;

use App\Enums\StatusPage\UpdateStatus;
use App\Models\Anomaly;
use App\Models\Update;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseMonitorUpdatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Anomaly $anomaly
    ) {}

    public function handle(): void
    {
        Update::query()
            ->whereHas('monitors', function ($query) {
                $query->where('monitor_id', $this->anomaly->monitor_id);
            })
            ->where('status', '!=', UpdateStatus::COMPLETED)
            ->where('created_at', '>=', $this->anomaly->started_at)
            ->update(['status' => UpdateStatus::COMPLETED]);
    }
} 