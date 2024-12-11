<?php

namespace App\Enums\Monitors;

use App\Jobs\Checks\HttpCheckJob;
use App\Jobs\Checks\IpCheckJob;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MonitorType: string implements HasLabel, HasColor
{
    case HTTP = 'http';
    case ICMP = 'icmp';
    case TCP = 'tcp';

    public function toCheckJob(): string
    {
        return match ($this) {
            self::HTTP => HttpCheckJob::class,
            self::ICMP => IpCheckJob::class,
        };
    }
    /**
     * @inheritDoc
     */
    public function getLabel(): string {
        return match ($this) {
            self::HTTP => 'HTTP',
            self::ICMP => 'ICMP',
            self::TCP => 'TCP',
        };
    }

    /**
     * @inheritDoc
     */
    public function getColor(): array|string|null {
        return match ($this) {
            self::HTTP => 'info',
            self::ICMP => 'danger',
            self::TCP => 'success',
        };
    }
}

