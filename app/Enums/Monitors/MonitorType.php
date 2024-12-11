<?php

namespace App\Enums\Monitors;

enum MonitorType: string
{
    case HTTP = 'http';
    case ICMP = 'icmp';
    case TCP = 'tcp';
}
