<?php

namespace App\Enums\Types;

enum AlertType: string
{
    case EMAIL = 'email';
    public function toNotificationChannel(): string
    {
        return match ($this) {
            self::EMAIL => 'email',
            default => throw new \InvalidArgumentException('Invalid alert type'),
        };
    }

}
