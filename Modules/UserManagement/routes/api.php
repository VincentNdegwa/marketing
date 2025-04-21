<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Controllers\UserManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('usermanagement', UserManagementController::class)->names('usermanagement');
});
