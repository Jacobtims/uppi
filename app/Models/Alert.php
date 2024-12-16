<?php

namespace App\Models;

use App\Enums\Types\AlertType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackRoute;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use NotificationChannels\Bird\BirdRoute;
use NotificationChannels\Messagebird\MessagebirdRoute;

class Alert extends Model
{
    use HasUlids, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'type' => AlertType::class,
        'config' => 'array',
    ];

    protected static function booted()
    {
        if (Auth::hasUser()) {
            static::addGlobalScope('user', function (Builder $builder) {
                $builder->where('user_id', Auth::id());
            });

            static::creating(function ($alert) {
                if (! $alert->user_id) {
                    $alert->user_id = Auth::id();
                }
            });
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class);
    }

    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    public function routeNotificationForMail(): ?string
    {
        if ($this->type !== AlertType::EMAIL) {
            return null;
        }

        return $this->destination;
    }

    public function routeNotificationForSlack(Notification $notification): ?SlackRoute
    {
        if ($this->type !== AlertType::SLACK) {
            return null;
        }

        if (! isset($this->config['slack_token'])) {
            throw new InvalidArgumentException('Slack token and channel are required');
        }

        return SlackRoute::make($this->destination, $this->config['slack_token']);
    }

    public function routeNotificationForMessagebird(Notification $notification): ?MessagebirdRoute
    {
        if ($this->type !== AlertType::MESSAGEBIRD) {
            return null;
        }

        if (! isset($this->config['bird_api_key']) || ! isset($this->config['bird_originator'])) {
            throw new InvalidArgumentException('Bird API key and originator are required');
        }

        if (empty($this->destination)) {
            throw new InvalidArgumentException('Destination is required');
        }

        return MessagebirdRoute::make([$this->destination], $this->config['bird_api_key'], $this->config['bird_originator']);
    }

    public function routeNotificationForPushover(Notification $notification): ?string
    {
        if ($this->type !== AlertType::PUSHOVER) {
            return null;
        }

        return $this->destination;
    }

    public function routeNotificationForBird(Notification $notification): ?BirdRoute
    {
        if ($this->type !== AlertType::BIRD) {
            return null;
        }

        if (! isset($this->config['bird_api_key']) || ! isset($this->config['bird_workspace_id']) || ! isset($this->config['bird_channel_id'])) {
            throw new InvalidArgumentException('Bird API key, workspace ID and channel ID are required');
        }

        if (empty($this->destination)) {
            throw new InvalidArgumentException('Destination is required');
        }

        return BirdRoute::make([$this->destination], $this->config['bird_api_key'], $this->config['bird_workspace_id'], $this->config['bird_channel_id']);
    }
}
