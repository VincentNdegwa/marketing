<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\App\Http\Controllers\CMSController;
use Modules\CMS\App\Http\Controllers\PageController;
use Modules\CMS\App\Http\Controllers\TemplateController;
use Modules\CMS\App\Http\Controllers\MediaController;

Route::middleware(['auth', 'verified'])->prefix('cms')->group(function () {
    // Main CMS routes
    Route::get('/', [CMSController::class, 'index'])->name('cms.index');
    
    // Pages routes
    Route::prefix('pages')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('cms.pages.index');
        Route::get('/create', [PageController::class, 'create'])->name('cms.pages.create');
        Route::post('/', [PageController::class, 'store'])->name('cms.pages.store');
        Route::get('/{page}/edit', [PageController::class, 'edit'])->name('cms.pages.edit');
        Route::put('/{page}', [PageController::class, 'update'])->name('cms.pages.update');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('cms.pages.destroy');
        Route::get('/{page}/preview', [PageController::class, 'preview'])->name('cms.pages.preview');
    });
    
    // Templates routes - will be implemented later
    Route::get('/templates', function() {
        return inertia('cms/templates/Index');
    })->name('cms.templates.index');
    
    // Media routes - will be implemented later
    Route::get('/media', function() {
        return inertia('cms/media/Index');
    })->name('cms.media.index');
    
    // Settings routes - will be implemented later
    Route::get('/settings', function() {
        return inertia('cms/settings/Index');
    })->name('cms.settings.index');
});

// Public routes for viewing pages
Route::get('page/{slug}', [PageController::class, 'show'])->name('cms.page.show');
