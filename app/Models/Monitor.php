<?php

namespace App\Models;

use App\Enums\Monitors\MonitorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitor extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'is_enabled' => 'boolean',
        'type' => MonitorType::class,
    ];

    public function alerts(): BelongsToMany
    {
        return $this->belongsToMany(Alert::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(Check::class);
    }
}
