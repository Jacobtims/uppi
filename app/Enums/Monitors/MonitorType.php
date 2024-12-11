<?php

namespace App\Enums\Monitors;

use App\Jobs\Checks\HttpCheckJob;
use App\Jobs\Checks\TcpCheckJob;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MonitorType: string implements HasLabel, HasIcon
{
    case HTTP = 'http';
    case TCP = 'tcp';

    public function toCheckJob(): string
    {
        return match ($this) {
            self::HTTP => HttpCheckJob::class,
            self::TCP => TcpCheckJob::class,
        };
    }
    /**
     * @inheritDoc
     */
    public function getLabel(): string {
        return match ($this) {
            self::HTTP => 'HTTP',
            self::TCP => 'TCP',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::HTTP => 'heroicon-o-globe-alt',
            self::TCP => 'heroicon-o-server-stack',
        };
    }
}

