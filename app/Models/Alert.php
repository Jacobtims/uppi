<?php

namespace App\Models;

use App\Enums\Types\AlertType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'type' => AlertType::class,
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }


}
