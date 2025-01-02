<?php

use App\Enums\Checks\Status;
use App\Jobs\Checks\HttpCheckJob;
use App\Models\Monitor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->monitor = Monitor::factory()->http()->create();
});

it('marks check as successful when http request succeeds', function () {
    Http::fake([
        '*' => Http::response('OK', 200, ['X-Test' => 'test']),
    ]);

    $job = new HttpCheckJob($this->monitor);
    $job->handle();

    expect($this->monitor->checks)->toHaveCount(1)
        ->and($this->monitor->checks->first())
        ->status->toBe(Status::OK)
        ->response_code->toBe(200)
        ->output->toBeJson()
        ->and(json_decode($this->monitor->checks->first()->output))
        ->reason->toBe('OK');
});

it('marks check as failed when http request fails', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $job = new HttpCheckJob($this->monitor);
    $job->handle();

    expect($this->monitor->checks)->toHaveCount(1)
        ->and($this->monitor->checks->first())
        ->status->toBe(Status::FAIL)
        ->response_code->toBe(500);
});

it('handles connection exceptions', function () {
    Http::fake([
        '*' => function() {
            throw new ConnectionException();
        },
    ]);

    $job = new HttpCheckJob($this->monitor);
    $job->handle();

    expect($this->monitor->checks)->toHaveCount(1)
        ->and($this->monitor->checks->first())
        ->status->toBe(Status::FAIL)
        ->output->toBeJson();
});

it('uses custom user agent when specified', function () {
    $this->monitor->user_agent = 'CustomBot/1.0';
    $this->monitor->save();

    Http::fake();

    $job = new HttpCheckJob($this->monitor);
    $job->handle();

    Http::assertSent(function ($request) {
        return $request->header('User-Agent')[0] === 'CustomBot/1.0';
    });
});
