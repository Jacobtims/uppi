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

    public function getAvailableStatuses(): array
    {
        return match ($this) {
            self::ANOMALY => [UpdateStatus::NEW, UpdateStatus::UNDER_INVESTIGATION, UpdateStatus::IDENTIFIED, UpdateStatus::WORK_IN_PROGRESS, UpdateStatus::RECOVERING, UpdateStatus::MONITORING, UpdateStatus::POST_INCIDENT],
            self::MAINTENANCE => [UpdateStatus::NEW, UpdateStatus::UNDER_INVESTIGATION, UpdateStatus::IDENTIFIED, UpdateStatus::WORK_IN_PROGRESS, UpdateStatus::MONITORING, UpdateStatus::COMPLETED],
            self::SCHEDULED_MAINTENANCE => [UpdateStatus::NEW, UpdateStatus::WORK_IN_PROGRESS, UpdateStatus::MONITORING, UpdateStatus::COMPLETED],
            self::UPDATE => [UpdateStatus::NEW, UpdateStatus::COMPLETED],
        };
    }
}

