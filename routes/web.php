<?php

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\Admin\ClientController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    // Get the current business from session if it exists
    $currentBusinessId = session('current_business_id');
    $user = Auth::user();

    if (! $currentBusinessId && $user) {
        $defaultBusiness = $user->defaultBusiness();
        if ($defaultBusiness) {
            $currentBusinessId = $defaultBusiness->id;
            session(['current_business_id' => $currentBusinessId]);
        }
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('super-admin')->middleware(['auth', 'verified', 'super'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard.admin');
    Route::resource('clients', ClientController::class);
    
    Route::get('modules', [ModuleController::class, 'index'])->name('admin.modules');
    Route::post('modules/{name}/enable', [ModuleController::class, 'enable'])->name('admin.modules.enable');
    Route::post('modules/{name}/disable', [ModuleController::class, 'disable'])->name('admin.modules.disable');

    Route::resource('businesses', BusinessController::class);
    Route::post('businesses/{business}/set-default', [BusinessController::class, 'setDefault'])->name('businesses.set-default');
    Route::post('businesses/{business}/switch', [BusinessController::class, 'switchBusiness'])->name('businesses.switch');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
