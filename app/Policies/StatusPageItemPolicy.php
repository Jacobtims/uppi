<?php

namespace App\Policies;

use App\Models\StatusPageItem;
use App\Models\User;

class StatusPageItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, StatusPageItem $statusPageItem): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, StatusPageItem $statusPageItem): bool
    {
        return $user->id === $statusPageItem->statusPage->user_id;
    }

    public function delete(User $user, StatusPageItem $statusPageItem): bool
    {
        return $user->id === $statusPageItem->statusPage->user_id;
    }

    public function restore(User $user, StatusPageItem $statusPageItem): bool
    {
        return $user->id === $statusPageItem->statusPage->user_id;
    }

    public function forceDelete(User $user, StatusPageItem $statusPageItem): bool
    {
        return $user->id === $statusPageItem->statusPage->user_id;
    }
}
