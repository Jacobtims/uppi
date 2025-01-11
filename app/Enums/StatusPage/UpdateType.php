<?php

namespace App\Enums\StatusPage;

enum UpdateType: string
{
    case ANOMALY = 'anomaly  ';
    case MAINTENANCE = 'maintenance';
    case SCHEDULED_MAINTENANCE = 'scheduled_maintenance';
    case UPDATE = 'update';
}
