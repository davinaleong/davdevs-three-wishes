<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishController;
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

Route::get('/test', function () {
    try {
        $user = Auth::user();
        $activeTheme = App\Services\ThemeService::getActiveTheme() ?: App\Services\ThemeService::getCurrentYearTheme();
        
        $wishes = $user->wishesForTheme($activeTheme)->get();
        $canEdit = App\Services\WishEditWindow::isOpen($activeTheme);
        $cutoffDescription = App\Services\WishEditWindow::getClosingDescription($activeTheme);
        
        return "Debug: User ID: {$user->id}, Theme: {$activeTheme->theme_title}, Wishes count: {$wishes->count()}, Can edit: " . ($canEdit ? 'yes' : 'no');
    } catch (Exception $e) {
        return "Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
    }
})->middleware(['auth', 'verified']);

// Wish routes - protected by auth and verified middleware
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('wishes', WishController::class);
    Route::patch('/wishes/reorder', [WishController::class, 'reorder'])->name('wishes.reorder');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
