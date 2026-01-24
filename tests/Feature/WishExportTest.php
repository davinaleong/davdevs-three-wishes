<?php

use App\Models\Theme;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('WishController@exportText', function () {
    it('displays text export for authenticated user', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        $wish = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'I wish to learn new skills',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.export.text'));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertHeader('Content-Disposition'); // Should have download header
    });

    it('requires authentication to access text export', function () {
        $response = $this->get(route('wishes.export.text'));

        $response->assertRedirect('/login');
    });

    it('requires email verification to access text export', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get(route('wishes.export.text'));

        $response->assertRedirect('/verify-email');
    });

    it('exports user wishes in text format with correct structure', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create([
            'year' => 2026, 
            'is_active' => true,
            'theme_title' => 'Test Theme',
            'theme_verse_text' => 'Test verse content',
            'theme_verse_reference' => 'Test 1:1'
        ]);
        
        Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'First test wish',
            'position' => 1,
        ]);
        
        Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'Second test wish',
            'position' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.export.text'));

        $content = $response->getContent();
        
        expect($content)
            ->toContain('TEST THEME - 2026')
            ->toContain('Test verse content')
            ->toContain('— Test 1:1')
            ->toContain('MY WISHES:')
            ->toContain('1. First test wish')
            ->toContain('2. Second test wish')
            ->toContain('Created with Dav/Devs Three Wishes © 2026');
    });

    it('handles empty wishes list in text export', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('wishes.export.text'));

        $content = $response->getContent();
        
        expect($content)->toContain('No wishes created yet for 2026');
    });

    it('generates correct filename for text export', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create([
            'year' => 2026, 
            'is_active' => true,
            'theme_title' => 'My Great Theme!'
        ]);

        $response = $this->actingAs($user)->get(route('wishes.export.text'));

        $disposition = $response->headers->get('Content-Disposition');
        expect($disposition)->toContain('wishes_my_great_theme_2026.txt');
    });

    it('does not display other users wishes in text export', function () {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        Wish::factory()->create([
            'user_id' => $user1->id,
            'theme_id' => $theme->id,
            'content' => 'User 1 wish',
            'position' => 1,
        ]);
        
        Wish::factory()->create([
            'user_id' => $user2->id,
            'theme_id' => $theme->id,
            'content' => 'User 2 wish',
            'position' => 1,
        ]);

        $response = $this->actingAs($user1)->get(route('wishes.export.text'));

        $content = $response->getContent();
        expect($content)
            ->toContain('User 1 wish')
            ->not->toContain('User 2 wish');
    });
});

describe('WishController@exportCsv', function () {
    it('displays csv export for authenticated user', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('wishes.export.csv'));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    });

    it('requires authentication to access csv export', function () {
        $response = $this->get(route('wishes.export.csv'));

        $response->assertRedirect('/login');
    });

    it('requires email verification to access csv export', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get(route('wishes.export.csv'));

        $response->assertRedirect('/verify-email');
    });

    it('exports user wishes in csv format with headers', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create([
            'year' => 2026, 
            'is_active' => true,
            'theme_title' => 'CSV Test Theme'
        ]);
        
        Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'CSV test wish',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.export.csv'));

        $content = $response->getContent();
        
        expect($content)
            ->toContain('Position,"Wish Content",Theme,Year,"Created Date"')
            ->toContain('1,"CSV test wish","CSV Test Theme",2026');
    });

    it('handles empty wishes list in csv export', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('wishes.export.csv'));

        $content = $response->getContent();
        
        // Should only contain header row
        $lines = explode("\n", trim($content));
        expect(count($lines))->toBe(1);
        expect($content)->toContain('Position,"Wish Content",Theme,Year,"Created Date"');
    });

    it('generates correct filename for csv export', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create([
            'year' => 2026, 
            'is_active' => true,
            'theme_title' => 'CSV Theme Title!'
        ]);

        $response = $this->actingAs($user)->get(route('wishes.export.csv'));

        $disposition = $response->headers->get('Content-Disposition');
        expect($disposition)->toContain('wishes_csv_theme_title_2026.csv');
    });

    it('exports wishes ordered by position in csv', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'Third wish',
            'position' => 3,
        ]);
        
        Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'First wish',
            'position' => 1,
        ]);
        
        Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'Second wish',
            'position' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.export.csv'));

        $content = $response->getContent();
        $lines = explode("\n", trim($content));
        
        // Skip header, check order
        expect($lines[1])->toContain('1,"First wish"');
        expect($lines[2])->toContain('2,"Second wish"');
        expect($lines[3])->toContain('3,"Third wish"');
    });

    it('does not display other users wishes in csv export', function () {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        Wish::factory()->create([
            'user_id' => $user1->id,
            'theme_id' => $theme->id,
            'content' => 'User 1 CSV wish',
            'position' => 1,
        ]);
        
        Wish::factory()->create([
            'user_id' => $user2->id,
            'theme_id' => $theme->id,
            'content' => 'User 2 CSV wish',
            'position' => 1,
        ]);

        $response = $this->actingAs($user1)->get(route('wishes.export.csv'));

        $content = $response->getContent();
        expect($content)
            ->toContain('User 1 CSV wish')
            ->not->toContain('User 2 CSV wish');
    });

    it('includes creation date in csv export', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        $wish = Wish::factory()->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
            'content' => 'Date test wish',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('wishes.export.csv'));

        $content = $response->getContent();
        $expectedDate = $wish->created_at->format('Y-m-d H:i:s');
        
        expect($content)->toContain($expectedDate);
    });
});