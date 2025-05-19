<?php

use App\Jobs\CloseMonitorUpdatesJob;
use App\Jobs\CreateMonitorUpdateJob;
use App\Models\Anomaly;
use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

beforeEach(function () {
    Bus::fake();
});

test('it dispatches create update job when anomaly is created', function () {
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create();

    $anomaly = Anomaly::factory()->for($monitor)->create();

    Bus::assertDispatched(CreateMonitorUpdateJob::class, fn ($job) => $job->anomaly->is($anomaly) && empty($job->anomaly->ended_at)
    );
});

test('it only dispatches close job when anomaly is resolved', function () {
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create();

    // Create anomaly without triggering observer
    Bus::fake();
    $anomaly = Anomaly::factory()
        ->for($monitor)
        ->startedAgo('1 hour')
        ->create();

    // Reset bus fake to catch the next dispatches
    Bus::fake();

    $anomaly->update(['ended_at' => now()]);

    // Should only dispatch close job, not create a new update
    Bus::assertDispatched(CloseMonitorUpdatesJob::class, fn ($job) => $job->anomaly->is($anomaly)
    );
    Bus::assertNotDispatched(CreateMonitorUpdateJob::class);
});

test('it does not dispatch jobs when other fields are updated', function () {
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create();

    // Create anomaly without triggering observer
    Bus::fake();
    $anomaly = Anomaly::factory()->for($monitor)->create();

    // Reset bus fake to catch the next dispatches
    Bus::fake();

    $anomaly->update(['started_at' => now()->subHour()]);

    Bus::assertNotDispatched(CloseMonitorUpdatesJob::class);
    Bus::assertNotDispatched(CreateMonitorUpdateJob::class);
});
