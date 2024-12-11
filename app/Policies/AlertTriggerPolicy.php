<?php

namespace App\Policies;

use App\Models\AlertTrigger;
use App\Models\User;

class AlertTriggerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AlertTrigger $alertTrigger): bool
    {
        return $user->id === $alertTrigger->monitor->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AlertTrigger $alertTrigger): bool
    {
        return $user->id === $alertTrigger->monitor->user_id;
    }

    public function delete(User $user, AlertTrigger $alertTrigger): bool
    {
        return $user->id === $alertTrigger->monitor->user_id;
    }

    public function restore(User $user, AlertTrigger $alertTrigger): bool
    {
        return $user->id === $alertTrigger->monitor->user_id;
    }

    public function forceDelete(User $user, AlertTrigger $alertTrigger): bool
    {
        return $user->id === $alertTrigger->monitor->user_id;
    }
}
