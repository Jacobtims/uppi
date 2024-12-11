<?php

namespace App\Models;

use App\Enums\Types\AlertType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Alert extends Model
{
    use HasUlids, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'type' => AlertType::class,
    ];

    protected static function booted()
    {
        if (Auth::hasUser()) {
            static::addGlobalScope('userMonitors', function (Builder $builder) {
                $builder->whereHas('monitors', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            });
        }
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class);
    }

    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    public function routeNotificationForMail(): string
    {
        return $this->destination;
    }

    public function routeNotificationForSlack(): string
    {
        return $this->destination;
    }
}
