<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Enums\Checks\Status;

class StatusPage extends Model
{
    use HasUlids;

    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'status' => Status::class,
    ];

    protected static function booted()
    {
        if (Auth::hasUser()) {
            static::addGlobalScope('user', function (Builder $builder) {
                $builder->where('user_id', Auth::id());
            });

            static::creating(function ($statusPage) {
                if (!$statusPage->user_id) {
                    $statusPage->user_id = Auth::id();
                }
            });
        }
    }

    public function items(): HasMany
    {
        return $this->hasMany(StatusPageItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOk(): bool
    {
        // If there are no enabled items, consider the status page as OK
        if ($this->items()->where('is_enabled', true)->doesntExist()) {
            return true;
        }

        // Check if any enabled monitor is down
        return !$this->items()
            ->where('is_enabled', true)
            ->whereHas('monitor', function ($query) {
                $query->where('status', Status::FAIL)
                    ->where('is_enabled', true);
            })
            ->exists();
    }

    public function getStatusAttribute(): Status
    {
        return $this->isOk() ? Status::OK : Status::FAIL;
    }
}
