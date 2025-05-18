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
    public function checkIn(Request $request, string $id)
    {
        $monitor = Monitor::findOrFail($id);
        
        // Validate the token if it exists on the monitor
        if ($monitor->pulse_token) {
            // Get token from query parameter OR from request body
            $token = $request->query('token') ?? $request->input('token');
            
            if (empty($token)) {
                throw ValidationException::withMessages([
                    'token' => ['Token is required for this pulse monitor.'],
                ]);
            }
            
            if (!Hash::check($token, $monitor->pulse_token)) {
                throw ValidationException::withMessages([
                    'token' => ['Invalid token for this pulse monitor.'],
                ]);
            }
        }
        
        // Update the last_checked_at timestamp to show the pulse was received
        $monitor->update([
            'last_checked_at' => now(),
        ]);
        
        // Return the response with the next expected check-in time
        return response()->json([
            'status' => 'success',
            'message' => 'Pulse check-in received',
            'next_check_expected_before' => now()->addMinutes($monitor->interval)->toDateTimeString(),
            'last_check_in' => $monitor->last_checked_at->toDateTimeString(),
        ]);
    }
} 