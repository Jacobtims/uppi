<?php

namespace App\Enums\Checks;

enum Status: string
{
    case OK = 'ok';
    case FAIL = 'fail';
    case UNKNOWN = 'unknown';
}
