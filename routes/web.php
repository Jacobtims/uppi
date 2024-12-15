<?php

use App\Http\Controllers\IconController;
use App\Http\Controllers\StatusPageController;
use Illuminate\Support\Facades\Route;

Route::get('/s/{statusPage:slug}', [StatusPageController::class, 'show'])->name('status-page.show');
Route::get('/s/{statusPage:slug}/status.json', [StatusPageController::class, 'statusJson'])->name('status-page.status-json');

Route::get('icon/{statusPageItem}', IconController::class)->name('icon')->middleware('signed');
