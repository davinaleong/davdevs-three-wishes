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
    
    // Email management (user functionality removed)
    Route::get('emails', [EmailController::class, 'index'])->name('emails.index');
    
    // Activity logs (export removed)
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});