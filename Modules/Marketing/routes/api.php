<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\App\Http\Controllers\MarketingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('marketing', MarketingController::class)->names('marketing');
});
