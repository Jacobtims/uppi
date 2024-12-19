<?php

namespace App\Http\Controllers\Api;

use App\Models\Anomaly;
use App\Models\Monitor;
use Illuminate\Http\JsonResponse;

class MonitorsController extends Controller
{

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Monitor::class);

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
        $this->authorize('view', $monitor);

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
        $this->authorize('view', $monitor);

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
        $this->authorize('view', $monitor);

        // Ensure the anomaly belongs to this monitor
        abort_if($anomaly->monitor_id !== $monitor->id, 404);

        return response()->json($anomaly->load([
            'checks' => function ($query) {
                $query->latest('checked_at');
            },
            'triggers.alert'
        ]));
    }
}
