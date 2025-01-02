<?php

namespace App\Enums\Types;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use NotificationChannels\Bird\BirdChannel;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Messagebird\MessagebirdChannel;
use NotificationChannels\Pushover\PushoverChannel;

enum AlertType: string implements HasIcon, HasLabel
{
    case EMAIL = 'email';
    case SLACK = 'slack';
    case BIRD = 'bird';
    case MESSAGEBIRD = 'messagebird';
    case PUSHOVER = 'pushover';
    case EXPO = 'expo';

    public function getLabel(): string
    {
        return match ($this) {
            self::EMAIL => 'E-mail',
            self::SLACK => 'Slack',
            self::BIRD => 'Bird',
            self::MESSAGEBIRD => 'Bird Connectivity Platform',
            self::PUSHOVER => 'Pushover',
            self::EXPO => 'Uppi app',
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
            self::EXPO => ExpoChannel::class,
            default => throw new \InvalidArgumentException('Invalid alert type'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EMAIL => 'heroicon-o-envelope',
            self::SLACK => 'heroicon-o-chat-bubble-left-right',
            self::BIRD => 'heroicon-o-megaphone',
            self::MESSAGEBIRD => 'heroicon-o-chat-bubble-oval-left-ellipsis',
            self::PUSHOVER => 'heroicon-o-bell',
            self::EXPO => 'heroicon-o-device-phone-mobile',
        };
    }
}
