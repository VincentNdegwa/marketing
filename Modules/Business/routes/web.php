<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\App\Http\Controllers\BusinessController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('business', BusinessController::class)->names('business');
});
