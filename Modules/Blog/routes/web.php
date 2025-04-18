<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\app\Http\Controllers\BlogController;


// Use standard middleware without the custom blog.inertia middleware
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::resource('blog', BlogController::class);
});
