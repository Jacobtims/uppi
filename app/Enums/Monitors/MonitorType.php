<?php

namespace App\Enums\Monitors;

use App\Jobs\Checks\HttpCheckJob;
use App\Jobs\Checks\IpCheckJob;

enum MonitorType: string
{
    case HTTP = 'http';
    case ICMP = 'icmp';
    case TCP = 'tcp';

    public function toCheckJob(): string
    {
        return match ($this) {
            self::HTTP => HttpCheckJob::class,
            self::ICMP => IpCheckJob::class,
            self::TCP => TcpCheckJob::class,
        };
    }
}
