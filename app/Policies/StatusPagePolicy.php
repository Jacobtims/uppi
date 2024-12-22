<?php

namespace App\Policies;

use App\Models\StatusPage;
use App\Models\User;

class StatusPagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, StatusPage $statusPage): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, StatusPage $statusPage): bool
    {
        return $user->id === $statusPage->user_id;
    }

    public function delete(User $user, StatusPage $statusPage): bool
    {
        return $user->id === $statusPage->user_id;
    }

    public function restore(User $user, StatusPage $statusPage): bool
    {
        return $user->id === $statusPage->user_id;
    }

    public function forceDelete(User $user, StatusPage $statusPage): bool
    {
        return $user->id === $statusPage->user_id;
    }
}
