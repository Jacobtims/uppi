<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use App\Models\Monitor;
use Illuminate\Support\Facades\Log;

class PulseCheckJob extends CheckJob
{
    public function __construct(protected Monitor $monitor)
    {
        parent::__construct($monitor);
    }

    protected function performCheck(): array
    {
        // Get the last check-in time
        $lastCheckedAt = $this->monitor->last_checked_at;
        
        // If no last check-in or the last check-in was more than the configured threshold time ago
        if ($lastCheckedAt === null || now()->diffInMinutes($lastCheckedAt) > $this->monitor->address) {
            return [
                'status' => Status::FAIL,
                'output' => 'Pulse check-in missed. Last check-in: ' . 
                    ($lastCheckedAt ? $lastCheckedAt->diffForHumans() : 'Never'),
            ];
        }
        
        return [
            'status' => Status::OK,
            'output' => 'Pulse check-in received within expected interval. Last check-in: ' . 
                $lastCheckedAt->diffForHumans(),
        ];
    }
} 