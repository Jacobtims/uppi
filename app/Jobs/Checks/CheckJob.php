<?php

namespace App\Jobs\Checks;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

abstract class CheckJob implements ShouldQueue
{
    use Queueable;
}
