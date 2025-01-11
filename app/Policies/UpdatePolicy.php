<?php

namespace App\Policies;

use App\Models\Update;
use App\Models\User;

class UpdatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Update $update): bool
    {
        return true;
    }
    
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Update $update): bool
    {
        return $user->id === $update->user_id;
    }
    
    public function delete(User $user, Update $update): bool
    {
        return $user->id === $update->user_id;
    }

    public function restore(User $user, Update $update): bool
    {
        return $user->id === $update->user_id;
    }

    public function forceDelete(User $user, Update $update): bool
    {
        return $user->id === $update->user_id;
    }
}
