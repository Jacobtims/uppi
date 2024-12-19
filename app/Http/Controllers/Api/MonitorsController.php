<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anomaly;
use App\Models\Monitor;
use Illuminate\Http\JsonResponse;

class MonitorsController extends Controller
{
    public function index(): JsonResponse
    {
        $monitors = Monitor::query()
            ->where('user_id', auth()->id())
            ->with(['lastCheck', 'anomalies' => function ($query) {
                $query->whereNull('ended_at');
            }])
            ->get();

        return response()->json($monitors);
    }

    public function show(Monitor $monitor): JsonResponse
    {
        return response()->json($monitor->load([
            'lastCheck',
            'anomalies' => function ($query) {
                $query->whereNull('ended_at');
            },
            'checks' => function ($query) {
                $query->latest('checked_at')->limit(10);
            }
        ]));
    }

    public function anomalies(Monitor $monitor): JsonResponse
    {
        $anomalies = $monitor->anomalies()
            ->with(['checks' => function ($query) {
                $query->latest('checked_at');
            }])
            ->latest('started_at')
            ->paginate(15);

        return response()->json($anomalies);
    }

    public function showAnomaly(Monitor $monitor, Anomaly $anomaly): JsonResponse
    {
        return response()->json($anomaly->load([
            'checks' => function ($query) {
                $query->latest('checked_at');
            },
            'triggers.alert'
        ]));
    }
}
