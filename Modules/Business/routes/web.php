<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\App\Http\Controllers\BusinessController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('business', BusinessController::class)->names('business');

        
    Route::post('business/{business}/set-default', [BusinessController::class, 'setAsDefault'])
        ->name('business.set-default');
        
    Route::post('business/{business}/switch', [BusinessController::class, 'switchBusiness'])
        ->name('business.switch');
});
