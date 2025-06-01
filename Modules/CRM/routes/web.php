<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\App\Http\Controllers\CRMController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('crm', CRMController::class)->names('crm');
});
