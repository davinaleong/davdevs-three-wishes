<?php

use App\Models\Theme;
use App\Models\User;
use App\Models\Wish;

it('can create a theme with required attributes', function () {
    $theme = Theme::factory()->create([
        'year' => 2026,
        'theme_title' => 'Year of Testing',
        'theme_tagline' => 'Trust and Test',
    ]);

    expect($theme->year)->toBe(2026)
        ->and($theme->theme_title)->toBe('Year of Testing')
        ->and($theme->theme_tagline)->toBe('Trust and Test');
});

it('has correct color methods', function () {
    $theme = Theme::factory()->create([
        'colors_json' => [
            'primary_color_hex' => '#2c3e50',
            'accent_color_hex' => '#3498db',
            'light_color_hex' => '#f8f9fa',
        ]
    ]);

    expect($theme->getColors('primary_color_hex'))->toBe('#2c3e50')
        ->and($theme->getColors('accent_color_hex'))->toBe('#3498db')
        ->and($theme->getColors('light_color_hex'))->toBe('#f8f9fa')
        ->and($theme->getColors('invalid'))->toBeNull();
});

it('can get active theme', function () {
    $activeTheme = Theme::factory()->create([
        'year' => now()->year,
        'is_active' => true,
    ]);

    $inactiveTheme = Theme::factory()->create([
        'year' => now()->year - 1,
        'is_active' => false,
    ]);

    expect(Theme::getActiveTheme()->id)->toBe($activeTheme->id);
});

it('can get theme for specific year', function () {
    $theme2026 = Theme::factory()->create(['year' => 2026]);
    $theme2025 = Theme::factory()->create(['year' => 2025]);

    expect(Theme::getThemeForYear(2026)->id)->toBe($theme2026->id)
        ->and(Theme::getThemeForYear(2025)->id)->toBe($theme2025->id);
});

it('has correct wishes relationship', function () {
    $theme = Theme::factory()->create();
    
    expect($theme->wishes())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});