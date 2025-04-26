<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Controllers\UserManagementController;
use Modules\UserManagement\App\Http\Controllers\UserRoleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix("user-management")->group(function(){
        Route::resource('/users', UserManagementController::class)->names('usermanagement');
        Route::resource('/roles',UserRoleController::class)->names('user-role');
    });
});
