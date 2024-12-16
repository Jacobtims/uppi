<?php

namespace App\Models;

use App\Enums\AlertTriggerType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertTrigger extends Model
{
    use HasUlids;

    protected $fillable = [
        'anomaly_id',
        'alert_id',
        'monitor_id',
        'type',
        'channels_notified',
        'metadata',
        'triggered_at',
    ];

    protected $casts = [
        'channels_notified' => 'array',
        'metadata' => 'array',
        'triggered_at' => 'datetime',
        'type' => AlertTriggerType::class,
    ];

    public function anomaly(): BelongsTo
    {
        return $this->belongsTo(Anomaly::class);
    }

    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
