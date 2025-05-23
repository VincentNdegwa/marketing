<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ModuleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
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

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
