<?php

namespace App\Enums\Types;

enum AlertType: string implements \Filament\Support\Contracts\HasLabel, \Filament\Support\Contracts\HasColor
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
