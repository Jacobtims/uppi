<?php

namespace App\Models;

use App\Enums\Monitors\MonitorType;
use App\Enums\Checks\Status;
use App\Jobs\Checks\CheckJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Monitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'type' => MonitorType::class,
        'status' => Status::class,
        'last_checked_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            $builder->where('user_id', Auth::id());
        });

        static::creating(function ($monitor) {
            if (!$monitor->user_id) {
                $monitor->user_id = Auth::id();
            }
        });
    }

    public function alerts(): BelongsToMany
    {
        return $this->belongsToMany(Alert::class);
    }

    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(Check::class);
    }

    public function makeCheckJob(): CheckJob
    {
        return new ($this->type->toCheckJob())($this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

