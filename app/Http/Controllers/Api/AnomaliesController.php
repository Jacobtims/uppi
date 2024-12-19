<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anomaly;
use Illuminate\Http\JsonResponse;

class AnomaliesController extends Controller
{
    public function index(): JsonResponse
    {
        $anomalies = Anomaly::query()
            ->with([
                'monitor',
                'checks' => function ($query) {
                    $query->latest('checked_at');
                }
            ])
            ->latest('started_at')
            ->paginate(15);

        return response()->json($anomalies);
    }

    public function show(Anomaly $anomaly): JsonResponse
    {
        return response()->json($anomaly->load([
            'monitor',
            'checks' => function ($query) {
                $query->latest('checked_at');
            },
            'triggers.alert'
        ]));
    }
}
