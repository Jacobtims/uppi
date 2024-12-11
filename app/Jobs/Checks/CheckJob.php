<?php

namespace App\Jobs\Checks;

use App\Models\Monitor;
use App\Models\Check;
use App\Enums\Checks\Status;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class CheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Monitor $monitor)
    {
    }

    abstract protected function performCheck(): array;

    public function handle(): void
    {
        $startTime = microtime(true);

        try {
            $result = $this->performCheck();
            $endTime = microtime(true);

            $this->createCheck(
                status: $result['status'] ?? Status::FAIL,
                responseTime: $this->calculateResponseTime($startTime, $endTime),
                responseCode: $result['response_code'] ?? null,
                output: $result['output'] ?? null
            );

        } catch (Exception $e) {
            $endTime = microtime(true);

            $this->createCheck(
                status: Status::FAIL,
                responseTime: $this->calculateResponseTime($startTime, $endTime),
                responseCode: null,
                output: $e->getMessage()
            );
        }
    }

    protected function createCheck(
        Status $status,
        float $responseTime,
        ?int $responseCode,
        ?string $output
    ): void {
        $check = new Check([
            'status' => $status,
            'response_time' => $responseTime,
            'response_code' => $responseCode,
            'output' => $output,
            'checked_at' => now(),
        ]);

        $this->monitor->checks()->save($check);

        $this->monitor->update(['status' => $status]);
    }

    protected function calculateResponseTime(float $startTime, float $endTime): float
    {
        return ($endTime - $startTime) * 1000; // Convert to milliseconds
    }
}
