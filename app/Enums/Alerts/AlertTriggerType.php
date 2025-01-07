<?php

namespace App\Enums\Alerts;

enum AlertTriggerType: string
{
    case DOWN = 'down';
    case RECOVERY = 'recovery';
}
