<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\App\Http\Controllers\BusinessController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('business', BusinessController::class)->names('business')->except('destroy');

    Route::delete('business/{business}', [BusinessController::class, 'destroy'])
        ->middleware(['password.confirm'])
        ->name('business.destroy');
        
    Route::post('business/{business}/set-default', [BusinessController::class, 'setAsDefault'])
        ->name('business.set-default');
        
    Route::post('business/{business}/switch', [BusinessController::class, 'switchBusiness'])
        ->name('business.switch');
});
