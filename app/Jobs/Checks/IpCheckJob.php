<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use App\Models\Check;
use Exception;

class IpCheckJob extends CheckJob
{
    public function handle(): void
    {
        $startTime = microtime(true);

        try {
            $target = $this->monitor->target;

            // Execute ping command (1 attempt, 5 second timeout)
            $cmd = "ping -c 1 -W 5 " . escapeshellarg($target);
            exec($cmd, $output, $returnCode);

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            // Parse ping time from output if successful
            if ($returnCode === 0) {
                $pingTime = 0;
                foreach ($output as $line) {
                    if (preg_match('/time=([0-9.]+) ms/', $line, $matches)) {
                        $pingTime = floatval($matches[1]);
                        break;
                    }
                }

                $check = new Check([
                    'status' => Status::UP,
                    'response_time' => $pingTime,
                    'response_code' => $returnCode,
                    'output' => implode("\n", $output),
                    'checked_at' => now(),
                ]);
            } else {
                throw new Exception("Ping failed with return code: " . $returnCode);
            }

            $this->monitor->checks()->save($check);

        } catch (Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $check = new Check([
                'status' => Status::DOWN,
                'response_time' => $responseTime,
                'response_code' => null,
                'output' => $e->getMessage(),
                'checked_at' => now(),
            ]);

            $this->monitor->checks()->save($check);
        }
    }
}
