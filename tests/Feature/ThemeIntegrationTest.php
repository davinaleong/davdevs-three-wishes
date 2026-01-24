<?php

use App\Models\Theme;
use App\Models\User;
use App\Models\Wish;
use App\Services\ThemeService;

it('displays themed welcome page', function () {
    $theme = Theme::factory()->create([
        'year' => now()->year,
        'is_active' => true,
        'theme_title' => 'Year of Testing',
        'colors_json' => ['primary_color_hex' => '#2c3e50'],
    ]);

    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertViewHas('activeTheme')
        ->assertViewHas('themeCssVariables')
        ->assertSee('Year of Testing')
        ->assertSee('#2c3e50');
});

it('applies theme colors to wishes interface', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create([
        'year' => now()->year,
        'is_active' => true,
        'colors_json' => [
            'primary_color_hex' => '#custom123',
            'accent_color_hex' => '#accent456'
        ],
    ]);

    $response = $this->actingAs($user)->get('/wishes');

    $response->assertStatus(200)
        ->assertSee('#custom123')
        ->assertSee('#accent456');
});

it('uses current year theme for new wishes', function () {
    $user = User::factory()->create();
    $currentYearTheme = Theme::factory()->create([
        'year' => now()->year,
        'is_active' => true,
    ]);
    
    $olderTheme = Theme::factory()->create([
        'year' => now()->year - 1,
        'is_active' => false,
    ]);

    $response = $this->actingAs($user)
        ->post('/wishes', [
            'content' => 'Test wish for current year',
            'position' => 1,
        ]);

    $response->assertRedirect('/wishes');

    $this->assertDatabaseHas('wishes', [
        'user_id' => $user->id,
        'theme_id' => $currentYearTheme->id,
        'content' => 'Test wish for current year',
    ]);
});

it('shows wishes grouped by theme year', function () {
    $user = User::factory()->create();
    
    $theme2026 = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
    $theme2025 = Theme::factory()->create(['year' => 2025, 'is_active' => false]);
    
    $wish2026 = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme2026->id,
        'content' => '2026 wish',
    ]);
    
    $wish2025 = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme2025->id,
        'content' => '2025 wish',
    ]);

    $response = $this->actingAs($user)->get('/wishes');

    $response->assertStatus(200)
        ->assertSee('2026 wish')
        ->assertDontSee('2025 wish'); // Only current year wishes are shown
});

it('theme service provides css variables', function () {
    $theme = Theme::factory()->create([
        'colors_json' => [
            'primary_color_hex' => '#123456',
            'accent_color_hex' => '#789abc',
            'light_color_hex' => '#def012',
        ],
    ]);

    $service = new ThemeService();
    $cssVariables = $service->getCssVariables($theme);

    expect($cssVariables)->toContain('--color-primary_color_hex: #123456')
        ->and($cssVariables)->toContain('--color-accent_color_hex: #789abc')
        ->and($cssVariables)->toContain('--color-light_color_hex: #def012');
});

it('fallback to default colors when no theme', function () {
    $theme = Theme::factory()->create();
    $service = new ThemeService();
    $cssVariables = $service->getCssVariables($theme);

    expect($cssVariables)->toBeString()
        ->and($cssVariables)->toContain(':root {');
});