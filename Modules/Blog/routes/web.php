<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\app\Http\Controllers\BlogController;

// Public routes for demonstration
Route::prefix('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
});

// Protected routes
Route::middleware(['auth', 'verified'])->prefix('blog')->group(function () {
    Route::get('/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/{id}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/{id}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/{id}', [BlogController::class, 'destroy'])->name('blog.destroy');
});
