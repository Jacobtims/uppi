<?php

use App\Enums\Checks\Status;
use App\Jobs\Checks\TcpCheckJob;
use App\Models\Monitor;
use App\Services\TcpConnectionService;

beforeEach(function () {
    $this->monitor = Monitor::factory()->tcp()->create();
    $this->tcpService = Mockery::mock(TcpConnectionService::class);
});

it('marks check as successful when tcp connection succeeds', function () {
    $socket = fopen('php://memory', 'r+');

    $this->tcpService->shouldReceive('connect')
        ->once()
        ->with($this->monitor->address, $this->monitor->port)
        ->andReturn($socket);

    $this->tcpService->shouldReceive('close')
        ->once()
        ->with($socket);

    $job = new TcpCheckJob($this->monitor, $this->tcpService);
    $job->handle();

    expect($this->monitor->checks)->toHaveCount(1)
        ->and($this->monitor->checks->first())
        ->status->toBe(Status::OK);
});

it('marks check as failed when tcp connection fails', function () {
    $this->tcpService->shouldReceive('connect')
        ->once()
        ->with($this->monitor->address, $this->monitor->port)
        ->andThrow(new Exception('Connection failed'));

    $job = new TcpCheckJob($this->monitor, $this->tcpService);
    $job->handle();

    expect($this->monitor->checks)->toHaveCount(1)
        ->and($this->monitor->checks->first())
        ->status->toBe(Status::FAIL)
        ->output->toBe('Connection failed');
});

it('uses default port 80 when not specified', function () {
    $this->monitor->port = null;
    $this->monitor->save();

    $socket = fopen('php://memory', 'r+');

    $this->tcpService->shouldReceive('connect')
        ->once()
        ->with($this->monitor->address, 80)
        ->andReturn($socket);

    $this->tcpService->shouldReceive('close')
        ->once()
        ->with($socket);

    $job = new TcpCheckJob($this->monitor, $this->tcpService);
    $job->handle();
});
