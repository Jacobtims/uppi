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
            $socket = $this->tcpService->connect(
                $this->monitor->address,
                $this->monitor->port ?? 80
            );

            $this->tcpService->close($socket);

            return [
                'status' => Status::OK,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
