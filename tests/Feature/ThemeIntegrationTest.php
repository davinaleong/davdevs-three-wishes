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
        'primary_color' => '#2c3e50',
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
        'primary_color' => '#custom123',
        'accent_color' => '#accent456',
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
            'wish_text' => 'Test wish for current year',
            'order' => 1,
        ]);

    $response->assertRedirect('/wishes');

    $this->assertDatabaseHas('wishes', [
        'user_id' => $user->id,
        'theme_id' => $currentYearTheme->id,
        'wish_text' => 'Test wish for current year',
    ]);
});

it('shows wishes grouped by theme year', function () {
    $user = User::factory()->create();
    
    $theme2026 = Theme::factory()->create(['year' => 2026]);
    $theme2025 = Theme::factory()->create(['year' => 2025]);
    
    $wish2026 = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme2026->id,
        'wish_text' => '2026 wish',
    ]);
    
    $wish2025 = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme2025->id,
        'wish_text' => '2025 wish',
    ]);

    $response = $this->actingAs($user)->get('/wishes');

    $response->assertStatus(200)
        ->assertSee('2026 wish')
        ->assertSee('2025 wish');
});

it('theme service provides css variables', function () {
    $theme = Theme::factory()->create([
        'primary_color' => '#123456',
        'accent_color' => '#789abc',
        'light_color' => '#def012',
    ]);

    $service = new ThemeService();
    $cssVariables = $service->getCssVariables($theme);

    expect($cssVariables)->toContain('--theme-primary: #123456')
        ->and($cssVariables)->toContain('--theme-accent: #789abc')
        ->and($cssVariables)->toContain('--theme-light: #def012');
});

it('fallback to default colors when no theme', function () {
    $service = new ThemeService();
    $cssVariables = $service->getCssVariables(null);

    expect($cssVariables)->toContain('--theme-primary: #2c3e50')
        ->and($cssVariables)->toContain('--theme-accent: #3498db');
});