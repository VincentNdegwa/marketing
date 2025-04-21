<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Controllers\UserManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('usermanagement', UserManagementController::class)->names('usermanagement');
});
