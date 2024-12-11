<?php

namespace App\Enums;

enum AlertTriggerType: string
{
    case DOWN = 'down';
    case RECOVERY = 'recovery';
}
