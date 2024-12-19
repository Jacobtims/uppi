<?php

use App\Http\Controllers\Api\AppTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('app/token', AppTokenController::class);
