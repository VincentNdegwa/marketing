<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\App\Http\Controllers\MarketingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('marketing', MarketingController::class)->names('marketing');
});
