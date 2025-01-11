<?php

namespace App\Enums\StatusPage;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UpdateType: string implements HasColor, HasLabel, HasIcon
{
    case ANOMALY = 'anomaly';
    case MAINTENANCE = 'maintenance';
    case SCHEDULED_MAINTENANCE = 'scheduled_maintenance';
    case UPDATE = 'update';

    public function getColor(): string
    {
        return match ($this) {
            self::ANOMALY => 'danger',
            self::MAINTENANCE => 'warning',
            self::SCHEDULED_MAINTENANCE => 'info',
            self::UPDATE => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ANOMALY => 'heroicon-o-exclamation-triangle',
            self::MAINTENANCE => 'heroicon-o-wrench',
            self::SCHEDULED_MAINTENANCE => 'heroicon-o-clock',
            self::UPDATE => 'heroicon-o-arrow-up',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ANOMALY => 'Anomaly',
            self::MAINTENANCE => 'Maintenance',
            self::SCHEDULED_MAINTENANCE => 'Scheduled Maintenance',
            self::UPDATE => 'Update',
        };
    }
}

