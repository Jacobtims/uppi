<?php

namespace App\Models;

use App\Enums\Types\AlertType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Anomaly extends Model
{
    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function notificationChannels(): array
    {
        return $this->monitor->alerts->pluck('type')->map(fn (AlertType $type) => $type->toNotificationChannel())->toArray();
    }
}
