<?php

namespace App\CacheTasks;

use Illuminate\Support\Collection;

interface RefreshStrategy
{
    /**
     * Get the constructor parameters for the refresh jobs
     *
     * @return Collection<array>
     */
    public function getConstructorParameters(string $userId): Collection;
}
