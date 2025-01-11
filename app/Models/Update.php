<?php

namespace App\Models;

use App\Enums\StatusPage\UpdateType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Update extends Model
{
    use HasUlids;
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
