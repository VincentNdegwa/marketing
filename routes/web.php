<?php

use App\Http\Controllers\Client\ClientController;
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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
