<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\App\Http\Controllers\BusinessController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('business', BusinessController::class)->names('business');
});
