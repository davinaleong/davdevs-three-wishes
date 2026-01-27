<?php

use App\Http\Controllers\Dev\EmailController;
use Illuminate\Support\Facades\Route;

// DEVELOPMENT EMAIL TESTING ROUTES - LOCAL AND TESTING ENVIRONMENT ONLY
if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev/emails')->middleware(['auth'])->name('dev.emails.')->group(function () {
        
        // Email testing dashboard
        Route::get('/dashboard', [EmailController::class, 'dashboard'])->name('dashboard');
        
        // Manual email triggers
        Route::get('/verification', [EmailController::class, 'sendVerification'])->name('verification');
        Route::get('/password-reset', [EmailController::class, 'sendPasswordReset'])->name('password-reset');
        Route::get('/welcome', [EmailController::class, 'sendWelcome'])->name('welcome');
        Route::get('/year-end-wishes', [EmailController::class, 'sendYearEndWishes'])->name('year-end-wishes');
        
        // Email previews (browser rendering)
        Route::get('/preview', [EmailController::class, 'previewEmail'])->name('preview');
    });
}