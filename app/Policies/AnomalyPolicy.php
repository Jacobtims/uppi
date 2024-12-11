<?php

namespace App\Policies;

use App\Models\Anomaly;
use App\Models\User;

class AnomalyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Anomaly $anomaly): bool
    {
        return $user->id === $anomaly->monitor->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Anomaly $anomaly): bool
    {
        return $user->id === $anomaly->monitor->user_id;
    }

    public function delete(User $user, Anomaly $anomaly): bool
    {
        return $user->id === $anomaly->monitor->user_id;
    }

    public function restore(User $user, Anomaly $anomaly): bool
    {
        return $user->id === $anomaly->monitor->user_id;
    }

    public function forceDelete(User $user, Anomaly $anomaly): bool
    {
        return $user->id === $anomaly->monitor->user_id;
    }
}
