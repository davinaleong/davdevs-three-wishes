<?php

use App\Services\WishEditWindow;
use App\Models\Theme;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-02-15 12:00:00'); // Mid February
});

afterEach(function () {
    Carbon::setTestNow();
});

it('allows editing during open period', function () {
    // Set test time to within 2026 editing period (before Feb 26)
    Carbon::setTestNow(Carbon::create(2026, 2, 15, 12, 0, 0, 'Asia/Singapore'));
    
    $theme = Theme::factory()->create([
        'year' => 2026,
    ]);

    expect(WishEditWindow::isOpen($theme))->toBeTrue();
});

it('disallows editing outside period', function () {
    // Set test time to after 2026 editing period (after Feb 26)
    Carbon::setTestNow(Carbon::create(2026, 3, 15, 12, 0, 0, 'Asia/Singapore'));
    
    $theme = Theme::factory()->create([
        'year' => 2026,
        'is_active' => true,
    ]);

    expect(WishEditWindow::isOpen($theme))->toBeFalse();
});

it('handles null edit window dates', function () {
    // Test past year - should always be closed
    Carbon::setTestNow(Carbon::create(2026, 2, 15, 12, 0, 0, 'Asia/Singapore'));
    
    $theme = Theme::factory()->create([
        'year' => 2025, // Past year
    ]);

    expect(WishEditWindow::isOpen($theme))->toBeFalse();
});

it('provides closing description', function () {
    // Set test time to within 2026 editing period
    Carbon::setTestNow(Carbon::create(2026, 2, 15, 12, 0, 0, 'Asia/Singapore'));
    
    $theme = Theme::factory()->create([
        'year' => 2026,
    ]);

    $description = WishEditWindow::getClosingDescription($theme);

    expect($description)->toBeString()
        ->and($description)->toContain('2026');
});

it('handles past edit window in description', function () {
    // Set current time to after editing period has ended
    Carbon::setTestNow(Carbon::create(2026, 3, 15, 12, 0, 0, 'Asia/Singapore'));
    
    $theme = Theme::factory()->create([
        'year' => 2026,
    ]);

    $description = WishEditWindow::getClosingDescription($theme);

    expect($description)->toContain('Editing closes');
});

it('allows editing for future year themes', function () {
    // Set test time to Jan 15, 2026 
    Carbon::setTestNow(Carbon::create(2026, 1, 15, 12, 0, 0, 'Asia/Singapore'));
    
    $futureTheme = Theme::factory()->create([
        'year' => 2027, // Future year
    ]);

    // Should allow editing until Jan 31 of the future year (2027)
    expect(WishEditWindow::isOpen($futureTheme))->toBeTrue();
});