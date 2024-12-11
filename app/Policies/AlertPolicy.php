<?php

namespace App\Policies;

use App\Models\Alert;
use App\Models\User;

class AlertPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Alert $alert): bool
    {
        // If the alert has no monitors, it's a new alert or unattached
        if ($alert->monitors()->count() === 0) {
            return true;
        }

        return $alert->monitors()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Alert $alert): bool
    {
        // If the alert has no monitors, it's a new alert or unattached
        if ($alert->monitors()->count() === 0) {
            return true;
        }

        return $alert->monitors()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Alert $alert): bool
    {
        // If the alert has no monitors, it's a new alert or unattached
        if ($alert->monitors()->count() === 0) {
            return true;
        }

        return $alert->monitors()->where('user_id', $user->id)->exists();
    }

    public function restore(User $user, Alert $alert): bool
    {
        return $this->update($user, $alert);
    }

    public function forceDelete(User $user, Alert $alert): bool
    {
        return $this->delete($user, $alert);
    }
}
