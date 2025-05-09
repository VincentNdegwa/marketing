<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\App\Http\Controllers\BuilderIOController;
use Modules\CMS\App\Http\Controllers\CMSController;
use Modules\CMS\App\Http\Controllers\PageController;
use Modules\CMS\App\Http\Controllers\TemplateController;
use Modules\CMS\App\Http\Controllers\MediaController;
use Modules\CMS\App\Http\Controllers\SettingsController;

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
    
    // Templates routes
    Route::prefix('templates')->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('cms.templates.index');
        Route::get('/create', [TemplateController::class, 'create'])->name('cms.templates.create');
        Route::post('/', [TemplateController::class, 'store'])->name('cms.templates.store');
        Route::get('/{template}/edit', [TemplateController::class, 'edit'])->name('cms.templates.edit');
        Route::put('/{template}', [TemplateController::class, 'update'])->name('cms.templates.update');
        Route::delete('/{template}', [TemplateController::class, 'destroy'])->name('cms.templates.destroy');
    });
    
    // Media routes
    Route::prefix('media')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('cms.media.index');
        Route::post('/upload', [MediaController::class, 'upload'])->name('cms.media.upload');
        Route::delete('/{media}', [MediaController::class, 'destroy'])->name('cms.media.destroy');
        Route::post('/folder', [MediaController::class, 'createFolder'])->name('cms.media.folder.create');
        Route::delete('/folder/{folder}', [MediaController::class, 'deleteFolder'])->name('cms.media.folder.delete');
    });
    
    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('cms.settings.index');
        Route::post('/', [SettingsController::class, 'update'])->name('cms.settings.update');
        Route::get('/{group}', [SettingsController::class, 'group'])->name('cms.settings.group');
    });

    Route::get('builderio', [BuilderIOController::class, 'index']);
});

// Public routes for viewing pages
Route::get('page/{slug}', [PageController::class, 'show'])->name('cms.page.show');
