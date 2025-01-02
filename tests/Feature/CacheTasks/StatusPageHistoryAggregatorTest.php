<?php

use App\Models\Check;
use App\Models\Monitor;
use App\Models\StatusPage;
use App\Models\StatusPageItem;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->statusPage = StatusPage::factory()->create([
        'user_id' => $this->user->id,
        'is_enabled' => true,
    ]);

    $this->monitor = Monitor::factory()->create([
        'user_id' => $this->user->id,
        'is_enabled' => true,
    ]);

    $this->item = StatusPageItem::factory()->create([
        'status_page_id' => $this->statusPage->id,
        'monitor_id' => $this->monitor->id,
        'is_enabled' => true,
    ]);
});

it('returns empty collection when no checks exist', function () {
    // Arrange
    $aggregator = new \App\CacheTasks\StatusPageHistoryAggregator($this->statusPage->id);
    $aggregator->forUser($this->user->id);

    // Act
    $result = $aggregator->execute();

    // Assert
    expect($result->get($this->item->id))
        ->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->toHaveCount(30)
        ->each->toBeNull();
});

it('returns correct status for each day', function () {
    // Arrange
    $today = now()->startOfDay();
    $yesterday = $today->copy()->subDay();

    // Create a check for yesterday
    Check::factory()->create([
        'monitor_id' => $this->monitor->id,
        'checked_at' => $yesterday,
        'status' => \App\Enums\Checks\Status::OK,
    ]);

    // Create a check for today (should be ignored as we only aggregate until yesterday)
    Check::factory()->create([
        'monitor_id' => $this->monitor->id,
        'checked_at' => $today,
        'status' => \App\Enums\Checks\Status::OK,
    ]);

    $aggregator = new \App\CacheTasks\StatusPageHistoryAggregator($this->statusPage->id);
    $aggregator->forUser($this->user->id);

    // Act
    $result = $aggregator->execute();
    $itemStatus = $result->get($this->item->id);

    // Assert
    expect($itemStatus->get($yesterday->toDateString()))->toBeTrue()
        ->and($itemStatus->get($today->toDateString()))->toBeNull();
});

it('returns status for specific item when id is provided', function () {
    // Arrange
    $otherItem = StatusPageItem::factory()->create([
        'status_page_id' => $this->statusPage->id,
        'monitor_id' => Monitor::factory()->create(['user_id' => $this->user->id]),
        'is_enabled' => true,
    ]);

    $aggregator = new \App\CacheTasks\StatusPageHistoryAggregator($this->statusPage->id);
    $aggregator->forUser($this->user->id)->forId($this->item->id);

    // Act
    $result = $aggregator->execute();

    // Assert
    expect($result)->toHaveCount(1)
        ->and($result)->toHaveKey($this->item->id)
        ->and($result)->not->toHaveKey($otherItem->id);
});

it('marks days with anomalies as down', function () {
    // Arrange
    $yesterday = now()->subDay()->startOfDay();

    // Create a check and an anomaly for yesterday
    Check::factory()->create([
        'monitor_id' => $this->monitor->id,
        'checked_at' => $yesterday,
        'status' => \App\Enums\Checks\Status::OK,
    ]);

    $this->monitor->anomalies()->create([
        'started_at' => $yesterday,
    ]);

    $aggregator = new \App\CacheTasks\StatusPageHistoryAggregator($this->statusPage->id);
    $aggregator->forUser($this->user->id);

    // Act
    $result = $aggregator->execute();

    // Assert
    expect($result->get($this->item->id)->get($yesterday->toDateString()))->toBeFalse();
});
