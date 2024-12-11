<?php

namespace App\Models;

use App\Enums\Checks\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Check extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'checked_at' => 'datetime',
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
        return $query->where('status', Status::DOWN);
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
