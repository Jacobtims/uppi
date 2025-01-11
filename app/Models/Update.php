<?php

namespace App\Models;

use App\Enums\StatusPage\UpdateType;
use App\Enums\StatusPage\UpdateStatus;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\UpdateObserver;
use App\Observers\UserIdObserver;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([UserIdObserver::class, UpdateObserver::class])]
class Update extends Model
{
    use HasUlids, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
        'is_published' => 'boolean',
        'type' => UpdateType::class,
        'status' => UpdateStatus::class,
        'is_featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusPages(): BelongsToMany
    {
        return $this->belongsToMany(StatusPage::class);
    }

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(Monitor::class);
    }
}
