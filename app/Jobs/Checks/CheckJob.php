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

    protected float $startTime;

    protected float $endTime;

    public function __construct(protected Monitor $monitor) {}

    public function handle(): void
    {
        $this->startTime = microtime(true);

        try {
            $result = $this->performCheck();
            $this->endTime = microtime(true);

            $this->processResult($result);
        } catch (ConnectionException|Exception $exception) {
            $this->handleException($exception);
        }

        $this->monitor->updateNextCheck();
    }

    abstract protected function performCheck(): array;

    protected function processResult(array $result): void
    {
        DB::transaction(function () use ($result) {
            $check = $this->createCheck($result);
            $this->updateMonitorStatus($check->status);
        });
    }

    protected function handleException(Exception $exception): void
    {
        $this->endTime = microtime(true);

        if (! $exception instanceof ConnectionException) {
            Log::error("Failed to perform monitor check {$this->monitor->id}: {$exception->getMessage()}");
            Sentry::captureException($exception);
        }

        $check = $this->createCheck([
            'status' => Status::FAIL,
            'output' => $exception->getMessage(),
        ]);

        $this->updateMonitorStatus($check->status);
    }

    protected function createCheck(array $result): Check
    {

        $check = new Check([
            'status' => $result['status'] ?? Status::FAIL,
            'response_time' => $this->calculateResponseTime(),
            'response_code' => $result['response_code'] ?? null,
            'output' => $result['output'] ?? null,
            'checked_at' => now(),
        ]);

        $this->monitor->checks()->save($check);

        return $check;
    }

    protected function updateMonitorStatus(Status $newStatus): void
    {
        $this->monitor->refresh();
        // Get the most recent checks, including the current one
        $recentChecks = $this->monitor->checks()
            ->latest('checked_at')
            ->take($this->monitor->consecutive_threshold)
            ->get();

        // Only update status if we have enough checks and they all have the same status
        if ($recentChecks->count() >= $this->monitor->consecutive_threshold) {
            $allSameStatus = $recentChecks->every(fn ($check) => $check->status === $newStatus);

            if ($allSameStatus) {
                $this->monitor->status = $newStatus;
                $this->monitor->save();
            }
        }
    }

    protected function calculateResponseTime(): float
    {
        return ($this->endTime - $this->startTime) * 1000; // Convert to milliseconds
    }
}
