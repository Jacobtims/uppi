<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use App\Models\Check;
use App\Models\Monitor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Sentry;

abstract class CheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Monitor $monitor)
    {
    }

    public function handle(): void
    {
        $startTime = microtime(true);

        try {
            $result = $this->performCheck();
            $endTime = microtime(true);

            $checkStatus = $result['status'] ?? Status::FAIL;
            $responseTime = $this->calculateResponseTime($startTime, $endTime);
            $responseCode = $result['response_code'] ?? null;
            $output = $result['output'] ?? null;

            DB::transaction(function () use ($checkStatus, $responseTime, $responseCode, $output) {
                // Create the check record
                $check = new Check([
                    'status' => $checkStatus,
                    'response_time' => $responseTime,
                    'response_code' => $responseCode,
                    'output' => $output,
                    'checked_at' => now(),
                ]);

                $this->monitor->checks()->save($check);

                // Count recent checks with the same status
                $recentChecks = $this->monitor->checks()
                    ->latest('checked_at')
                    ->take($this->monitor->consecutive_threshold)
                    ->get();

                // Only update status if we have enough consecutive checks with the same status
                if ($recentChecks->count() >= $this->monitor->consecutive_threshold &&
                    $recentChecks->every(fn($check) => $check->status === $checkStatus)) {
                    $this->monitor->update(['status' => $checkStatus]);
                }
            });

        } catch (ConnectionException $exception) {
            $this->fail($exception, $startTime);
        } catch (Exception $exception) {
            $this->fail($exception, $startTime);

            Log::error('Failed to perform monitor check ' . $this->monitor->id . ': ' . $exception->getMessage());
            Sentry::captureException($exception);
        }
    }

    abstract protected function performCheck(): array;

    protected function calculateResponseTime(float $startTime, float $endTime): float
    {
        return ($endTime - $startTime) * 1000; // Convert to milliseconds
    }

    protected function fail(Exception $exception, $startTime): void
    {
        $endTime = microtime(true);

        DB::transaction(function () use ($startTime, $endTime, $exception) {
            // Create the check record
            $check = new Check([
                'status' => Status::FAIL,
                'response_time' => $this->calculateResponseTime($startTime, $endTime),
                'response_code' => null,
                'output' => $exception->getMessage(),
                'checked_at' => now(),
            ]);

            $this->monitor->checks()->save($check);

            // Count recent failures
            $recentChecks = $this->monitor->checks()
                ->latest('checked_at')
                ->take($this->monitor->consecutive_threshold)
                ->get();

            // Only update status if we have enough consecutive failures
            if ($recentChecks->count() >= $this->monitor->consecutive_threshold &&
                $recentChecks->every(fn($check) => $check->status === Status::FAIL)) {
                $this->monitor->update(['status' => Status::FAIL]);
            }
        });
    }
}
