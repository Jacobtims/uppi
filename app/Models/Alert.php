<?php

namespace App\Models;

use App\Enums\Types\AlertType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Alert extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => AlertType::class,
    ];

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class);
    }


}
