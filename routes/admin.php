<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\ActivityLogController;
use Illuminate\Support\Facades\Route;

// Admin guest routes (login)
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Protected admin routes
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index']);
    
    // Theme management
    Route::resource('themes', ThemeController::class);
    Route::post('themes/{theme}/activate', [ThemeController::class, 'activate'])->name('themes.activate');
    
    // User management
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/activity', [UserController::class, 'activityLogs'])->name('users.activity');
    Route::post('users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
    Route::post('users/{user}/send-year-end', [UserController::class, 'sendYearEndWishes'])->name('users.send-year-end');
    
    // Email management
    Route::get('emails', [EmailController::class, 'index'])->name('emails.index');
    Route::post('emails/broadcast', [EmailController::class, 'sendBroadcast'])->name('emails.broadcast');
    Route::post('emails/year-end-batch', [EmailController::class, 'sendYearEndBatch'])->name('emails.year-end-batch');
    
    // Activity logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});