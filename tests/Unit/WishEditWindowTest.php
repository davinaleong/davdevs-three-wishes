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
    $theme = Theme::factory()->create([
        'year' => 2026,
        'edit_window_start' => '2026-01-01',
        'edit_window_end' => '2026-03-31',
    ]);

    expect(WishEditWindow::isOpen($theme))->toBeTrue();
});

it('disallows editing outside period', function () {
    $theme = Theme::factory()->create([
        'year' => 2026,
        'edit_window_start' => '2026-01-01',
        'edit_window_end' => '2026-02-01', // Before current test date
    ]);

    expect(WishEditWindow::isOpen($theme))->toBeFalse();
});

it('handles null edit window dates', function () {
    $theme = Theme::factory()->create([
        'year' => 2026,
        'edit_window_start' => null,
        'edit_window_end' => null,
    ]);

    // Should default to allow editing for current year
    expect(WishEditWindow::isOpen($theme))->toBeTrue();
});

it('provides closing description', function () {
    $theme = Theme::factory()->create([
        'year' => 2026,
        'edit_window_start' => '2026-01-01',
        'edit_window_end' => '2026-03-31',
    ]);

    $description = WishEditWindow::getClosingDescription($theme);

    expect($description)->toBeString()
        ->and($description)->toContain('March 31');
});

it('handles past edit window in description', function () {
    $theme = Theme::factory()->create([
        'year' => 2026,
        'edit_window_start' => '2026-01-01',
        'edit_window_end' => '2026-02-01', // Past date
    ]);

    $description = WishEditWindow::getClosingDescription($theme);

    expect($description)->toContain('window has closed');
});

it('allows editing for future year themes', function () {
    $futureTheme = Theme::factory()->create([
        'year' => 2027,
        'edit_window_start' => '2027-01-01',
        'edit_window_end' => '2027-03-31',
    ]);

    // Should allow editing for future themes if within window
    expect(WishEditWindow::isOpen($futureTheme))->toBeFalse(); // Not yet 2027
});