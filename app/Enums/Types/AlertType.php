<?php

namespace App\Enums\Types;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum AlertType: string implements HasLabel, HasIcon
{
    case EMAIL = 'email';
    case SLACK = 'slack';
    case BIRD = 'messagebird';
    public function getLabel(): string
    {
        return match ($this) {
            self::EMAIL => 'E-mail',
            self::SLACK => 'Slack',
            self::BIRD => 'Bird',
        };
    }

    public function toNotificationChannel(): string
    {
        return match ($this) {
            self::EMAIL => 'email',
            self::SLACK => 'slack',
            self::BIRD => 'messagebird',
            default => throw new \InvalidArgumentException('Invalid alert type'),
        };
    }

    public function getIcon(): string|null
    {
        return match ($this) {
            self::EMAIL => 'heroicon-o-envelope',
            self::SLACK => 'heroicon-o-chat-bubble-left-right',
            self::BIRD => 'heroicon-o-device-phone-mobile',
        };
    }
}
