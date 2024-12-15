<?php

use App\Http\Controllers\IconController;
use App\Http\Controllers\StatusPageController;
use Illuminate\Support\Facades\Route;

Route::get('/s/{statusPage:slug}', StatusPageController::class)->name('status-page.show');

Route::get('icon/{statusPageItem}', IconController::class)->name('icon')->middleware('signed');
