<?php

use App\Enums\Checks\Status;
use App\Enums\Monitors\MonitorType;
use App\Jobs\Checks\DummyCheckJob;
use App\Jobs\TriggerAlertJob;
use App\Models\Check;
use App\Models\Monitor;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

function createMonitor(array $attributes = []): Monitor
{
    return Monitor::factory()->create(array_merge([
        'type' => MonitorType::DUMMY,
        'status' => Status::UNKNOWN,
        'consecutive_threshold' => 2,
        'interval' => 1,
        'is_enabled' => true,
        'next_check_at' => now(),
        'user_id' => User::factory(),
        'address' => 'https://example.com',
        'port' => null,
    ], $attributes));
}

beforeEach(function () {
    Bus::fake();
});

it('creates a check record', function () {
    $monitor = createMonitor();
    $job = new DummyCheckJob($monitor);
    $job->handle();

    expect($monitor->checks)->toHaveCount(1)
        ->and($monitor->checks->first())
        ->toBeInstanceOf(Check::class)
        ->status->toBe(Status::OK)
        ->response_code->toBe(200)
        ->output->toBe('test output');

    Bus::assertDispatched(TriggerAlertJob::class);
});

it('handles exceptions gracefully', function () {
    $monitor = createMonitor();
    $job = new class($monitor) extends DummyCheckJob
    {
        protected function performCheck(): array
        {
            throw new Exception('Test exception');
        }
    };

    $job->handle();

    expect($monitor->checks)->toHaveCount(1)
        ->and($monitor->checks->first())
        ->status->toBe(Status::FAIL)
        ->output->toBe('Test exception');

    Bus::assertDispatched(TriggerAlertJob::class);
});

it('updates next check time after completion', function () {
    $monitor = createMonitor();
    $originalNextCheck = $monitor->next_check_at;

    $job = new DummyCheckJob($monitor);
    $job->handle();

    expect($monitor->fresh()->next_check_at)
        ->not->toBe($originalNextCheck);

    Bus::assertDispatched(TriggerAlertJob::class);
});

it('calculates response time correctly', function () {
    $monitor = createMonitor();
    $job = new DummyCheckJob($monitor);
    $job->handle();

    expect($monitor->checks->first()->response_time)
        ->toBeFloat()
        ->toBeGreaterThan(0)
        ->toBeLessThan(1000); // Should be less than 1 second for a dummy check

    Bus::assertDispatched(TriggerAlertJob::class);
});
