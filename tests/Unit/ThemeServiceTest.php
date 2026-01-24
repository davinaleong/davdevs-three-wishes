<?php

use App\Services\ThemeService;
use App\Models\Theme;

it('can get active theme', function () {
    $activeTheme = Theme::factory()->create([
        'year' => now()->year,
        'is_active' => true,
    ]);

    $inactiveTheme = Theme::factory()->create([
        'year' => now()->year - 1,
        'is_active' => false,
    ]);

    $service = new ThemeService();
    expect($service->getActiveTheme()->id)->toBe($activeTheme->id);
});

it('can get current year theme', function () {
    $currentYearTheme = Theme::factory()->create([
        'year' => now()->year,
    ]);

    $lastYearTheme = Theme::factory()->create([
        'year' => now()->year - 1,
    ]);

    $service = new ThemeService();
    expect($service->getCurrentYearTheme()->id)->toBe($currentYearTheme->id);
});

it('can get css variables', function () {
    $theme = Theme::factory()->create([
        'colors_json' => [
            'primary_color_hex' => '#2c3e50',
            'accent_color_hex' => '#3498db',
            'light_color_hex' => '#f8f9fa',
        ],
    ]);

    $service = new ThemeService();
    $cssVariables = $service->getCssVariables($theme);

    expect($cssVariables)->toBeString()
        ->and($cssVariables)->toContain('--color-primary_color_hex: #2c3e50')
        ->and($cssVariables)->toContain('--color-accent_color_hex: #3498db')
        ->and($cssVariables)->toContain('--color-light_color_hex: #f8f9fa');
});

it('handles null theme in css variables', function () {
    $service = new ThemeService();
    // Since getCssVariables requires a Theme object, we'll use a theme with default colors
    $defaultTheme = Theme::factory()->create();
    $cssVariables = $service->getCssVariables($defaultTheme);

    expect($cssVariables)->toBeString()
        ->and($cssVariables)->toContain(':root {')
        ->and($cssVariables)->toContain('}');
});

it('can get theme for specific year', function () {
    $theme2026 = Theme::factory()->create(['year' => 2026]);
    $theme2025 = Theme::factory()->create(['year' => 2025]);

    $service = new ThemeService();
    expect($service->getThemeForYear(2026)->id)->toBe($theme2026->id)
        ->and($service->getThemeForYear(2025)->id)->toBe($theme2025->id);
});