<?php

namespace App\Models;

use App\Enums\Checks\Status;
use App\Observers\CheckObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([CheckObserver::class])]
class Check extends Model
{
    protected $guarded = [];

    protected $casts = [
        'checked_at' => 'datetime',
        'status' => Status::class,
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('checked_at', 'desc');
    }

    public function scopeFail(Builder $query): Builder
    {
        return $query->where('status', Status::FAIL);
    }

    public function scopeOk(Builder $query): Builder
    {
        return $query->where('status', Status::OK);
    }

    public function scopeUnknown(Builder $query): Builder
    {
        return $query->where('status', Status::UNKNOWN);
    }
}
