<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use App\Models\Check;
use Illuminate\Support\Facades\Http;
use Exception;

class HttpCheckJob extends CheckJob
{
    public function handle(): void
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(30)
                ->get($this->monitor->address);

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

            $check = new Check([
                'status' => $response->successful() ? Status::UP : Status::DOWN,
                'response_time' => $responseTime,
                'response_code' => $response->status(),
                'output' => $response->body(),
                'checked_at' => now(),
            ]);

            $this->monitor->checks()->save($check);

        } catch (Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $check = new Check([
                'status' => Status::DOWN,
                'response_time' => $responseTime,
                'response_code' => null,
                'output' => $e->getMessage(),
                'checked_at' => now(),
            ]);

            $this->monitor->checks()->save($check);
        }
    }
}
