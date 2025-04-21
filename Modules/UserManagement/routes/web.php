<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Controllers\UserManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('user-management', UserManagementController::class)->names('usermanagement');
});
