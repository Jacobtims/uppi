<?php

use App\Http\Controllers\IconController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\StatusPageController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/s/{statusPage:slug}', [StatusPageController::class, 'show'])->name('status-page.show');
Route::get('/s/{statusPage:slug}/status.json', [StatusPageController::class, 'statusJson'])->name('status-page.status-json');
Route::get('/s/{statusPage:slug}/embed', [StatusPageController::class, 'embed'])->name('status-page.embed');

Route::get('icon/{statusPageItem}', IconController::class)->name('icon')->middleware('signed');

Route::get('/embed/{user}/embed.js', function (User $user) {
    return response()
        ->view('js.embed', [
            'user' => $user,
        ], 200)
        ->header('Content-Type', 'application/javascript')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('embed.js');

Route::get('/privacy', PrivacyController::class)->name('privacy');
