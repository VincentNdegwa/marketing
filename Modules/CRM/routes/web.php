<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\App\Http\Controllers\CRMController;
use Modules\CRM\App\Http\Controllers\ContactController;
use Modules\CRM\App\Http\Controllers\CompanyController;
use Modules\CRM\App\Http\Controllers\DealController;
use Modules\CRM\App\Http\Controllers\ActivityController;
use Modules\CRM\App\Http\Controllers\TaskController;
use Modules\CRM\App\Http\Controllers\NoteController;

Route::middleware(['auth', 'verified'])->prefix('crm')->name('crm.')->group(function () {
    // Dashboard
    Route::get('/crm/dashboard', [CRMController::class, 'index'])->name('crm.dashboard');
    
    // Contacts
    Route::resource('contacts', ContactController::class);
    
    // Companies
    Route::resource('companies', CompanyController::class);
    
    // Deals
    Route::resource('deals', DealController::class);
    
    // Activities
    Route::resource('activities', ActivityController::class);
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    
    // Notes
    Route::resource('notes', NoteController::class)->except(['edit', 'create']);
    
    // Nested resources
    Route::prefix('contacts/{contact}')->name('contacts.')->group(function () {
        Route::resource('activities', ActivityController::class)->only(['index', 'store']);
        Route::resource('tasks', TaskController::class)->only(['index', 'store']);
        Route::resource('notes', NoteController::class)->only(['index', 'store']);
        Route::resource('deals', DealController::class)->only(['index', 'store']);
    });
    
    Route::prefix('companies/{company}')->name('companies.')->group(function () {
        Route::resource('activities', ActivityController::class)->only(['index', 'store']);
        Route::resource('tasks', TaskController::class)->only(['index', 'store']);
        Route::resource('notes', NoteController::class)->only(['index', 'store']);
        Route::resource('contacts', ContactController::class)->only(['index', 'store']);
    });
    
    Route::prefix('deals/{deal}')->name('deals.')->group(function () {
        Route::resource('activities', ActivityController::class)->only(['index', 'store']);
        Route::resource('tasks', TaskController::class)->only(['index', 'store']);
        Route::resource('notes', NoteController::class)->only(['index', 'store']);
    });
});

// Legacy route for backward compatibility
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('crm', CRMController::class)->names('crm');
});
