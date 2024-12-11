<?php

namespace App\Enums\Types;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AlertType: string implements HasLabel, HasColor
{
    case EMAIL = 'email';

    public function getLabel(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
        };
    }

    public function toNotificationChannel(): string
    {
        return match ($this) {
            self::EMAIL => 'email',
            default => throw new \InvalidArgumentException('Invalid alert type'),
        };
    }
    /**
     * @inheritDoc
     */
    public function getColor(): array|string|null
    {
        return match ($this) {
            self::EMAIL => 'info',
        };
    }
}
