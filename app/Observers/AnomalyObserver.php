<?php

namespace App\Observers;

use App\Jobs\CloseMonitorUpdatesJob;
use App\Jobs\CreateMonitorUpdateJob;
use App\Models\Anomaly;

class AnomalyObserver
{
    public function created(Anomaly $anomaly): void
    {
        dispatch(new CreateMonitorUpdateJob($anomaly));
    }

    public function updated(Anomaly $anomaly): void
    {
        // Only close updates when ended_at is set (monitor recovered)
        if ($anomaly->wasChanged('ended_at') && $anomaly->ended_at !== null) {
            dispatch(new CloseMonitorUpdatesJob($anomaly));
        }
    }
} 