<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\App\Http\Controllers\MarketingController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::resource('marketing', MarketingController::class)->names('marketing');
    Route::prefix('marketing')->group(function () {
      Route::resource('facebook', MarketingController::class)->names('marketing.facebook');
    });
});
