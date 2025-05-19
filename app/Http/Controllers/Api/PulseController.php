<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PulseController extends Controller
{
    /**
     * Handle a pulse check-in for the given monitor URL.
     *
     * @param Request $request
     * @param string $id The monitor ID to check-in for
     * @return Response
     */
    public function checkIn(Monitor $monitor)
    {   
        $monitor->update([
            'last_checkin_at' => now(),
        ]);
        
        return response()->json([
            'status' => 'success',
            'next_check_expected_before' => now()->addMinutes((int)$monitor->address)->toDateTimeString(),
            'last_check_in' => $monitor->last_checkin_at->toDateTimeString(),
            'monitor' => $monitor,
        ]);
    }
} 