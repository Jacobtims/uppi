<?php

use App\CacheTasks\CacheTask;
use App\CacheTasks\ResponseTimeAggregator;
use App\CacheTasks\StatusPageHistoryAggregator;
use Illuminate\Support\Facades\Cache;

it('caches data with correct TTL', function () {
    // Arrange
    $task = new ResponseTimeAggregator();
    $task->forUser('test-user');

    // Act
    $task->get();

    // Assert
    expect(Cache::has("user_test-user_{$task->key()}"))->toBeTrue();
});

it('refreshes cache data', function () {
    // Arrange
    $task = new ResponseTimeAggregator();
    $task->forUser('test-user');

    // Act
    $task->refresh();

    // Assert
    expect(Cache::has("user_test-user_{$task->key()}"))->toBeTrue();
});

it('requires user scope', function () {
    // Arrange
    $task = new ResponseTimeAggregator();

    // Act & Assert
    expect(fn () => $task->get())->toThrow(RuntimeException::class);
});

it('has correct TTL values', function () {
    expect(ResponseTimeAggregator::getTtl())->toBe(60)
        ->and(StatusPageHistoryAggregator::getTtl())->toBe(60);
});
