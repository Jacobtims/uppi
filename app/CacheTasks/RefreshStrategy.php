<?php

namespace App\CacheTasks;

use Illuminate\Support\Collection;

interface RefreshStrategy
{
    /**
     * Get the refresh jobs for this cache task
     */
    public function getRefreshJobs(string $userId): Collection;
}
