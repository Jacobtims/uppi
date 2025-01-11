<?php

namespace App\Models;

use App\Enums\StatusPage\UpdateType;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\UpdateObserver;

#[ObservedBy(UpdateObserver::class)]

class Update extends Model
{
    use HasUlids, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
        'is_published' => 'boolean',
        'type' => UpdateType::class,
        'is_featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
