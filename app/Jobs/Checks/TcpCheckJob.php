<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use Exception;

class TcpCheckJob extends CheckJob
{
    protected function performCheck(): array
    {
        $socket = @fsockopen(
            $this->monitor->address,
            $this->monitor->port ?? 80,
            $errno,
            $errstr,
            timeout: 5
        );

        if (! $socket) {
            throw new Exception($errstr, $errno);
        }

        fclose($socket);

        return [
            'status' => Status::OK,
            'response_code' => 0,
            'output' => "Successfully connected to {$this->monitor->address}:{$this->monitor->port}",
        ];
    }
}
