<?php

use App\Http\Controllers\Api\AnomaliesController;
use App\Http\Controllers\Api\AppTokenController;
use App\Http\Controllers\Api\MonitorsController;
use App\Http\Controllers\Api\PulseController;
use App\Http\Controllers\Api\PushController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('app/token', AppTokenController::class);

// Public route for pulse check-in that doesn't require authentication
Route::any('pulse/{monitor}', [PulseController::class, 'checkIn'])->middleware('signed')->name('pulse.checkin');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('monitors', [MonitorsController::class, 'index']);
    Route::get('monitors/{monitor}', [MonitorsController::class, 'show']);
    Route::get('monitors/{monitor}/anomalies', [AnomaliesController::class, 'index']);
    Route::get('monitors/{monitor}/anomalies/{anomaly}', [AnomaliesController::class, 'show']);

    Route::get('anomalies', [AnomaliesController::class, 'index']);
    Route::get('anomalies/{anomaly}', [AnomaliesController::class, 'show']);

    Route::put('push', [PushController::class, 'store']);
    Route::delete('push', [PushController::class, 'destroy']);
});
