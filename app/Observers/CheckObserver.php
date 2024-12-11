<?php

namespace App\Observers;

use App\Models\Check;
use App\Jobs\TriggerAlertJob;

class CheckObserver
{
    /**
     * Handle the Check "created" event.
     */
    public function created(Check $check): void
    {
        // Update monitor status
        $check->monitor->update([
            'status' => $check->status,
        ]);

        // Dispatch job to handle alert triggering
        TriggerAlertJob::dispatch($check);
    }

    /**
     * Handle the Check "updated" event.
     */
    public function updated(Check $check): void
    {
        //
    }

    /**
     * Handle the Check "deleted" event.
     */
    public function deleted(Check $check): void
    {
        //
    }

    /**
     * Handle the Check "restored" event.
     */
    public function restored(Check $check): void
    {
        //
    }

    /**
     * Handle the Check "force deleted" event.
     */
    public function forceDeleted(Check $check): void
    {
        //
    }
}
