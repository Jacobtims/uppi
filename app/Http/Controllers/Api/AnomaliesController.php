<?php

namespace App\Http\Controllers\Api;

use App\Models\Anomaly;
use App\Models\Monitor;
use Illuminate\Http\JsonResponse;

class AnomaliesController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Monitor::class);

        $anomalies = Anomaly::query()
            ->with([
                'monitor',
                'checks' => function ($query) {
                    $query->latest('checked_at');
                },
            ])
            ->latest('started_at')
            ->paginate(15);

        return response()->json($anomalies);
    }

    public function show(Anomaly $anomaly): JsonResponse
    {
        $this->authorize('view', $anomaly->monitor);

        return response()->json($anomaly->load([
            'monitor',
            'checks' => function ($query) {
                $query->latest('checked_at');
            },
            'triggers.alert',
        ]));
    }
}
