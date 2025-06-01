<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\App\Http\Controllers\MarketingController;
use Modules\Marketing\App\Http\Controllers\FacebookMarketingController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::resource('marketing', MarketingController::class)->names('marketing');
    Route::prefix('marketing')->group(function () {
      Route::resource('facebook', FacebookMarketingController::class)->names('marketing.facebook');
    });
});
