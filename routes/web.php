<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Admin\ModuleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix("super-admin")->group(function(){
    Route::get('dashboard', function(){
        return Inertia::render('Dashboard');
    })->middleware(['auth','verified'])->name('dashboard.admin');
    Route::resource("clients", ClientController::class);
    
    Route::get('modules', [ModuleController::class, 'index'])->middleware(['auth','verified'])->name('admin.modules');
    Route::post('modules/{name}/enable', [ModuleController::class, 'enable'])->middleware(['auth','verified'])->name('admin.modules.enable');
    Route::post('modules/{name}/disable', [ModuleController::class, 'disable'])->middleware(['auth','verified'])->name('admin.modules.disable');
})->middleware(['super']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
