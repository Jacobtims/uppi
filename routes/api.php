<?php

use App\Http\Controllers\Api\AnomaliesController;
use App\Http\Controllers\Api\AppTokenController;
use App\Http\Controllers\Api\MonitorsController;
use App\Http\Controllers\Api\PushController;
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
    Route::get('monitors/{monitor}/anomalies', [AnomaliesController::class, 'index']);
    Route::get('monitors/{monitor}/anomalies/{anomaly}', [AnomaliesController::class, 'show']);

    // Push notification routes
    Route::put('push', [PushController::class, 'store']);
    Route::delete('push', [PushController::class, 'destroy']);
});
