<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeCssVariables = $themeService->getCssVariables($activeTheme);
    
    return view('welcome', compact('activeTheme', 'themeCssVariables'));
});

Route::get('/dashboard', function () {
    return redirect()->route('wishes.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Two-Factor Authentication routes
Route::middleware('auth')->group(function () {
    Route::get('/two-factor/challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    
    Route::middleware('verified')->group(function () {
        Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
        Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
        Route::get('/two-factor/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');
        Route::post('/two-factor/regenerate-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate-codes');
        Route::delete('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    });
});

// Wish routes - protected by auth and verified middleware
Route::middleware(['auth', 'verified'])->group(function () {
    Route::patch('/wishes/reorder', [WishController::class, 'reorder'])->name('wishes.reorder');
    Route::get('/wishes/print', [WishController::class, 'print'])->name('wishes.print');
    Route::get('/wishes/export/text', [WishController::class, 'exportText'])->name('wishes.export.text');
    Route::get('/wishes/export/csv', [WishController::class, 'exportCsv'])->name('wishes.export.csv');
    Route::resource('wishes', WishController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Legal pages - publicly accessible
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');

require __DIR__.'/auth.php';

// Include development email testing routes
if (app()->environment('local')) {
    require __DIR__.'/dev-email.php';
}
