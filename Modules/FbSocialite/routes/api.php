<?php

use Illuminate\Support\Facades\Route;
use Modules\FbSocialite\App\Http\Controllers\FbSocialiteController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('fbsocialite', FbSocialiteController::class)->names('fbsocialite');
});
