<?php

namespace App\Policies;

use App\Models\Check;
use App\Models\User;

class CheckPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Check $check): bool
    {
        return $user->id === $check->monitor->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Check $check): bool
    {
        return $user->id === $check->monitor->user_id;
    }

    public function delete(User $user, Check $check): bool
    {
        return $user->id === $check->monitor->user_id;
    }

    public function restore(User $user, Check $check): bool
    {
        return $user->id === $check->monitor->user_id;
    }

    public function forceDelete(User $user, Check $check): bool
    {
        return $user->id === $check->monitor->user_id;
    }
}
