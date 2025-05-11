<?php

use Illuminate\Support\Facades\Route;
use Modules\GoogleSocialite\App\Http\Controllers\GoogleController;

Route::middleware(['auth', 'verified'])->group(function () {
});
Route::prefix('google')->name('google.')->group(function () {
    Route::get('auth', [GoogleController::class, 'redirectToGoogle'])->name('login');
    Route::get('callback', [GoogleController::class, 'handleGoogleCallback'])->name('callback');
});
