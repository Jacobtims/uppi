<?php

namespace App\Enums\Monitors;

use App\Jobs\Checks\DummyCheckJob;
use App\Jobs\Checks\HttpCheckJob;
use App\Jobs\Checks\PulseCheckJob;
use App\Jobs\Checks\TcpCheckJob;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MonitorType: string implements HasIcon, HasLabel
{
    case HTTP = 'http';
    case TCP = 'tcp';
    case DUMMY = 'dummy';
    case PULSE = 'pulse';

    public function toCheckJob(): string
    {
        return match ($this) {
            self::HTTP => HttpCheckJob::class,
            self::TCP => TcpCheckJob::class,
            self::DUMMY => DummyCheckJob::class,
            self::PULSE => PulseCheckJob::class,
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::HTTP => 'HTTP',
            self::TCP => 'TCP',
            self::DUMMY => '',
            self::PULSE => 'Check-in',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::HTTP => 'heroicon-o-globe-alt',
            self::TCP => 'heroicon-o-server-stack',
            self::DUMMY => 'heroicon-o-question-mark-circle',
            self::PULSE => 'heroicon-o-clock',
        };
    }

    public static function options(): array
    {
        return [
            self::HTTP->value => self::HTTP->getLabel(),
            self::TCP->value => self::TCP->getLabel(),
            self::PULSE->value => self::PULSE->getLabel(),
        ];
    }
}
