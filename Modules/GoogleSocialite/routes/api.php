<?php

use Illuminate\Support\Facades\Route;
use Modules\GoogleSocialite\App\Http\Controllers\GoogleSocialiteController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('googlesocialite', GoogleSocialiteController::class)->names('googlesocialite');
});
