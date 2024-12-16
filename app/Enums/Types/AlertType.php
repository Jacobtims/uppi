<?php

namespace App\Enums\Types;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use NotificationChannels\Bird\BirdChannel;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Pushover\PushoverChannel;

enum AlertType: string implements HasLabel, HasIcon
{
    case EMAIL = 'email';
    case SLACK = 'slack';
    case BIRD = 'bird';
    case MESSAGEBIRD = 'messagebird';
    case PUSHOVER = 'pushover';

    public function getLabel(): string
    {
        return match ($this) {
            self::EMAIL => 'E-mail',
            self::SLACK => 'Slack',
            self::BIRD => 'Bird',
            self::MESSAGEBIRD => 'Bird Connectivity Platform',
            self::PUSHOVER => 'Pushover',
        };
    }

    public function toNotificationChannel(): string
    {
        return match ($this) {
            self::EMAIL => 'mail',
            self::SLACK => 'slack',
            self::BIRD => BirdChannel::class,
            self::MESSAGEBIRD => MessagebirdChannel::class,
            self::PUSHOVER => PushoverChannel::class,
            default => throw new \InvalidArgumentException('Invalid alert type'),
        };
    }

    public function getIcon(): string|null
    {
        return match ($this) {
            self::EMAIL => 'heroicon-o-envelope',
            self::SLACK => 'heroicon-o-chat-bubble-left-right',
            self::BIRD => 'heroicon-o-device-phone-mobile',
            self::MESSAGEBIRD => 'heroicon-o-chat-bubble-oval-left-ellipsis',
            self::PUSHOVER => 'heroicon-o-bell',
        };
    }
}
