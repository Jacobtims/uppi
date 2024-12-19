<?php

use App\Http\Controllers\Api\AnomaliesController;
use App\Http\Controllers\Api\AppTokenController;
use App\Http\Controllers\Api\MonitorsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('app/token', AppTokenController::class);

Route::middleware('auth:sanctum')->group(function () {
    // Monitor routes
    Route::get('monitors', [MonitorsController::class, 'index']);
    Route::get('monitors/{monitor}', [MonitorsController::class, 'show']);
    Route::get('monitors/{monitor}/anomalies', [MonitorsController::class, 'anomalies']);
    Route::get('monitors/{monitor}/anomalies/{anomaly}', [MonitorsController::class, 'showAnomaly']);

    // Account-wide anomalies routes
    Route::get('anomalies', [AnomaliesController::class, 'index']);
    Route::get('anomalies/{anomaly}', [AnomaliesController::class, 'show']);
});
