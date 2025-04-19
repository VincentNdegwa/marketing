<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\app\Http\Controllers\BlogController;
use Modules\Blog\app\Http\Controllers\BlogsTrendController;
use Modules\Blog\app\Http\Controllers\BlogsCategoryController;

// Use standard middleware without the custom blog.inertia middleware
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::resource('blog', BlogController::class);
    Route::resource('blogs-category', BlogsCategoryController::class);
    Route::resource('trends', BlogsTrendController::class);
});
