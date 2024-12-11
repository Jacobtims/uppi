<?php

namespace App\Jobs\Checks;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

abstract class CheckJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Monitor $monitor)
    {
        //
    }

    abstract public function handle(): void;
}
