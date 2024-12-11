<?php

namespace App\Jobs\Checks;

use App\Enums\Checks\Status;
use Acamposm\Ping\Ping;
use Acamposm\Ping\PingCommandBuilder;
use Exception;

class IpCheckJob extends CheckJob
{
    protected function performCheck(): array
    {
        $ping = new Ping($this->monitor->address);
        $ping->setCount(3) // Number of ping attempts
             ->setInterval(0.2) // Time between pings
             ->setTimeout(5); // Timeout in seconds

        $result = $ping->run();

        if ($result->hasError()) {
            throw new Exception($result->errorMessage);
        }

        // Get the average round trip time
        $avgRtt = $result->avgRtt;

        // If we got here, at least one ping was successful
        return [
            'status' => Status::UP,
            'response_code' => $result->packetsReceived,
            'output' => "Ping successful. Min/Avg/Max = {$result->minRtt}/{$result->avgRtt}/{$result->maxRtt} ms. {$result->packetsReceived}/{$result->packetsSent} packets received.",
        ];
    }
}
