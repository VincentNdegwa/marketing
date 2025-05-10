<?php

use Illuminate\Support\Facades\Route;
use Modules\FbSocialite\App\Http\Controllers\FaceBookController;
use Modules\FbSocialite\App\Http\Controllers\FbSocialiteController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('fbsocialite', FbSocialiteController::class)->names('fbsocialite');
    Route::prefix('facebook')->name('facebook.')->group(function () {
        Route::get('auth', [FaceBookController::class, 'loginUsingFacebook'])->name('login');
        Route::get('callback', [FaceBookController::class, 'callbackFromFacebook'])->name('callback');
    });
});
