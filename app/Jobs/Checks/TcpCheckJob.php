<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use App\Services\TcpConnectionService;
use Exception;

class TcpCheckJob extends CheckJob
{
    protected TcpConnectionService $tcpService;

    public function __construct($monitor, ?TcpConnectionService $tcpService = null)
    {
        parent::__construct($monitor);
        $this->tcpService = $tcpService ?? new TcpConnectionService();
    }

    protected function performCheck(): array
    {
        try {
            $timing = microtime(true);
            $socket = $this->tcpService->connect(
                $this->monitor->address,
                $this->monitor->port ?? 80
            );
            $this->tcpService->close($socket);
            $duration = microtime(true) - $timing;

            return [
                'status' => Status::OK,
                'duration' => $duration,
            ];
        } catch (Exception $e) {
            return [
                'status' => Status::FAIL,
                'output' => [
                    'error' => $e->getMessage(),
                ],
            ];
        }
    }
}
