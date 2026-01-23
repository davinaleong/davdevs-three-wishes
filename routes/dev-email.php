<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Services\ThemeService;

// TEMPORARY EMAIL TESTING ROUTES - REMOVE IN PRODUCTION
if (app()->environment('local')) {
    Route::prefix('dev/emails')->middleware(['auth'])->group(function () {
        
        // Manual email verification trigger
        Route::get('/verification', function () {
            $user = Auth::user();
            $user->sendEmailVerificationNotification();
            return back()->with('success', 'Verification email sent!');
        })->name('dev.emails.verification');
        
        // Manual password reset trigger
        Route::get('/password-reset', function () {
            $user = Auth::user();
            $status = Password::sendResetLink(['email' => $user->email]);
            
            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('success', 'Password reset email sent!');
            }
            return back()->with('error', 'Failed to send password reset email');
        })->name('dev.emails.password-reset');
        
        // Manual year-end wishes email trigger (placeholder)
        Route::get('/year-end-wishes', function () {
            $user = Auth::user();
            $activeTheme = ThemeService::getActiveTheme();
            $wishes = $user->wishesForTheme($activeTheme)->get();
            
            // TODO: Create and send year-end wishes email
            // For now, just return success
            return back()->with('success', 'Year-end wishes email would be sent! (Not implemented yet)');
        })->name('dev.emails.year-end-wishes');
        
        // Email testing dashboard
        Route::get('/dashboard', function () {
            return view('dev.emails.dashboard');
        })->name('dev.emails.dashboard');
    });
}