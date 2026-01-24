<?php

use App\Models\User;
use App\Models\Theme;
use App\Models\Wish;

describe('WishController@print', function () {
    it('displays print view for authenticated user', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewIs('wishes.print')
            ->assertViewHas('wishes')
            ->assertViewHas('activeTheme')
            ->assertViewHas('layout');
    });

    it('requires authentication to access print view', function () {
        $response = $this->get(route('wishes.print'));

        $response->assertRedirect('/login');
    });

    it('requires email verification to access print view', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertRedirect('/verify-email');
    });

    it('displays user wishes for current theme year', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $activeTheme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        $inactiveTheme = Theme::factory()->create(['year' => 2025, 'is_active' => false]);
        
        // Create wishes for active theme
        $activeWish1 = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $activeTheme->id,
            'content' => 'Active theme wish 1',
            'position' => 1,
        ]);
        
        $activeWish2 = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $activeTheme->id,
            'content' => 'Active theme wish 2',
            'position' => 2,
        ]);
        
        // Create wish for inactive theme (should not appear)
        $inactiveWish = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $inactiveTheme->id,
            'content' => 'Inactive theme wish',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewHas('wishes', function ($wishes) use ($activeWish1, $activeWish2, $inactiveWish) {
                return $wishes->contains($activeWish1) && 
                       $wishes->contains($activeWish2) && 
                       !$wishes->contains($inactiveWish);
            });
    });

    it('does not display other users wishes', function () {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        // Create wish for user1
        $user1Wish = Wish::factory()->create([
            'user_id' => $user1->id,
            'theme_id' => $theme->id,
            'content' => 'User 1 wish',
        ]);
        
        // Create wish for user2
        $user2Wish = Wish::factory()->create([
            'user_id' => $user2->id,
            'theme_id' => $theme->id,
            'content' => 'User 2 wish',
        ]);

        $response = $this->actingAs($user1)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewHas('wishes', function ($wishes) use ($user1Wish, $user2Wish) {
                return $wishes->contains($user1Wish) && !$wishes->contains($user2Wish);
            });
    });

    it('returns wishes ordered by position', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        // Create wishes in random order
        $wish3 = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'position' => 3,
        ]);
        
        $wish1 = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'position' => 1,
        ]);
        
        $wish2 = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'position' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewHas('wishes', function ($wishes) {
                $positions = $wishes->pluck('position')->toArray();
                return $positions === [1, 2, 3]; // Should be ordered by position
            });
    });

    it('uses default layout when no layout parameter provided', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Theme::factory()->create(['year' => 2026, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewHas('layout', 'portrait-a4');
    });

    it('accepts custom layout parameter', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Theme::factory()->create(['year' => 2026, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('wishes.print', ['layout' => 'landscape-letter']));

        $response->assertStatus(200)
            ->assertViewHas('layout', 'landscape-letter');
    });

    it('passes active theme to view', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $activeTheme = Theme::factory()->create([
            'year' => 2026,
            'is_active' => true,
            'theme_title' => 'Test Active Theme',
            'theme_verse_text' => 'Test verse text',
        ]);
        
        // Create an inactive theme to ensure we get the right one
        Theme::factory()->create(['year' => 2025, 'is_active' => false]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewHas('activeTheme', function ($theme) use ($activeTheme) {
                return $theme->id === $activeTheme->id && 
                       $theme->theme_title === 'Test Active Theme';
            });
    });

    it('works with no wishes for user', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewHas('wishes', function ($wishes) {
                return $wishes->isEmpty();
            });
    });

    it('uses current year theme as fallback when no active theme', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        // Create a theme for current year but not marked as active
        $currentYearTheme = Theme::factory()->create(['year' => 2026, 'is_active' => false]);
        
        // Create theme for different year
        Theme::factory()->create(['year' => 2025, 'is_active' => false]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertViewHas('activeTheme', function ($theme) use ($currentYearTheme) {
                return $theme->id === $currentYearTheme->id;
            });
    });

    it('renders print view with theme colors', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create([
            'year' => 2026,
            'is_active' => true,
            'colors_json' => [
                'primary' => '#123456',
                'secondary' => '#789ABC',
                'accent' => '#DEF012',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertSee('#123456', false) // Check for primary color in output
            ->assertSee('#789ABC', false) // Check for secondary color in output
            ->assertSee('#DEF012', false); // Check for accent color in output
    });

    it('displays theme information in print view', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create([
            'year' => 2026,
            'is_active' => true,
            'theme_title' => 'Test Theme Title',
            'theme_verse_text' => 'This is a test verse',
            'theme_verse_reference' => 'Test 1:1',
        ]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertSee('Test Theme Title')
            ->assertSee('This is a test verse')
            ->assertSee('Test 1:1')
            ->assertSee('2026');
    });

    it('displays user wish content in print view', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        $wish = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'This is my special test wish content',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.print'));

        $response->assertStatus(200)
            ->assertSee('This is my special test wish content');
    });
});