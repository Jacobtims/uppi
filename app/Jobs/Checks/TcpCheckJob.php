<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use App\Models\Check;
use Exception;

class TcpCheckJob extends CheckJob
{
    public function handle(): void
    {
        $startTime = microtime(true);

        try {
            $socket = @fsockopen(
                $this->monitor->target,
                $this->monitor->port ?? 80,
                $errno,
                $errstr,
                30
            );

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            if (!$socket) {
                throw new Exception($errstr, $errno);
            }

            fclose($socket);

            $check = new Check([
                'status' => Status::UP,
                'response_time' => $responseTime,
                'response_code' => 0,
                'output' => "Successfully connected to {$this->monitor->target}:{$this->monitor->port}",
                'checked_at' => now(),
            ]);

            $this->monitor->checks()->save($check);

        } catch (Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $check = new Check([
                'status' => Status::DOWN,
                'response_time' => $responseTime,
                'response_code' => $e->getCode(),
                'output' => $e->getMessage(),
                'checked_at' => now(),
            ]);

            $this->monitor->checks()->save($check);
        }
    }
}
