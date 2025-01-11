<?php

use App\Enums\StatusPage\UpdateStatus;
use App\Enums\StatusPage\UpdateType;
use App\Jobs\CloseMonitorUpdatesJob;
use App\Jobs\CreateMonitorUpdateJob;
use App\Models\Anomaly;
use App\Models\Monitor;
use App\Models\StatusPage;
use App\Models\StatusPageItem;
use App\Models\Update;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates update with default text when no custom values', function () {    
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create(['auto_create_update' => true]);
    $statusPage = StatusPage::factory()->for($user)->create();
    StatusPageItem::factory()
        ->for($statusPage)
        ->for($monitor)
        ->create(['is_enabled' => true]);
    
    $anomaly = Anomaly::factory()->for($monitor)->create();

    $update = $statusPage->updates()->first();
    expect($statusPage->updates()->count())->toBe(1)
        ->and($update)
        ->title->toBe("{$monitor->name} is experiencing issues")
        ->type->toBe(UpdateType::ANOMALY)
        ->status->toBe(UpdateStatus::UNDER_INVESTIGATION)
        ->is_published->toBeTrue()
        ->user_id->toBe($user->id);

    $update->refresh();

    expect($update->monitors->pluck('id')->contains($monitor->id))->toBeTrue()
        ->and($update->statusPages->pluck('id')->contains($statusPage->id))->toBeTrue();
});

test('it creates update with custom text when provided', function () {    
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create([
        'auto_create_update' => true,
        'update_values' => [
            'title' => 'Custom title for :monitor_name at :monitor_address',
            'content' => 'Custom content for :monitor_name of type :monitor_type',
        ],
    ]);
    $statusPage = StatusPage::factory()->for($user)->create();
    StatusPageItem::factory()
        ->for($statusPage)
        ->for($monitor)
        ->create(['is_enabled' => true]);
    
    $anomaly = Anomaly::factory()->for($monitor)->create();

    $update = $statusPage->updates()->first();
    expect($statusPage->updates()->count())->toBe(1)
        ->and($update)
        ->title->toBe("Custom title for {$monitor->name} at {$monitor->address}")
        ->content->toBe("Custom content for {$monitor->name} of type {$monitor->type->value}")
        ->type->toBe(UpdateType::ANOMALY)
        ->status->toBe(UpdateStatus::UNDER_INVESTIGATION)
        ->is_published->toBeTrue()
        ->user_id->toBe($user->id);

    $update->refresh();

    expect($update->monitors->pluck('id')->contains($monitor->id))->toBeTrue()
        ->and($update->statusPages->pluck('id')->contains($statusPage->id))->toBeTrue();
});

test('it skips update creation when auto create is disabled', function () {
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create(['auto_create_update' => false]);
    $statusPage = StatusPage::factory()->for($user)->create();
    StatusPageItem::factory()
        ->for($statusPage)
        ->for($monitor)
        ->create(['is_enabled' => true]);
    
    $anomaly = Anomaly::factory()->for($monitor)->create();

    CreateMonitorUpdateJob::dispatchSync($anomaly);

    expect($monitor->updates()->count())->toBe(0);
});

test('it skips update creation when no status pages', function () {
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create(['auto_create_update' => true]);
    $anomaly = Anomaly::factory()->for($monitor)->create();

    CreateMonitorUpdateJob::dispatchSync($anomaly);

    expect($monitor->updates()->count())->toBe(0);
});

test('it closes open updates when anomaly is resolved', function () {
    $user = User::factory()->create();
    $monitor = Monitor::factory()->for($user)->create();
    $anomaly = Anomaly::factory()
        ->for($monitor)
        ->startedAgo('1 hour')
        ->create();
    
    // Create updates in different states
    $openUpdate = Update::factory()->create([
        'status' => UpdateStatus::UNDER_INVESTIGATION,
        'created_at' => $anomaly->started_at->addMinutes(5),
    ]);
    $openUpdate->monitors()->attach($monitor);

    $completedUpdate = Update::factory()->create([
        'status' => UpdateStatus::COMPLETED,
        'created_at' => $anomaly->started_at->addMinutes(10),
    ]);
    $completedUpdate->monitors()->attach($monitor);

    $oldUpdate = Update::factory()->create([
        'status' => UpdateStatus::UNDER_INVESTIGATION,
        'created_at' => $anomaly->started_at->subDay(),
    ]);
    $oldUpdate->monitors()->attach($monitor);

    CloseMonitorUpdatesJob::dispatchSync($anomaly);

    // Verify updates are closed but no new updates are created
    expect($openUpdate->fresh()->status)->toBe(UpdateStatus::COMPLETED)
        ->and($completedUpdate->fresh()->status)->toBe(UpdateStatus::COMPLETED)
        ->and($oldUpdate->fresh()->status)->toBe(UpdateStatus::UNDER_INVESTIGATION)
        ->and($monitor->updates()->count())->toBe(3);
}); 