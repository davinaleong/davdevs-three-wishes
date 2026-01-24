<?php

use App\Models\User;
use App\Models\Theme;
use App\Models\Wish;

it('displays wishes index page', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create(['year' => now()->year, 'is_active' => true]);

    $response = $this->actingAs($user)->get(route('wishes.index'));

    $response->assertStatus(200)
        ->assertViewIs('wishes.index')
        ->assertViewHas('wishes')
        ->assertViewHas('activeTheme')
        ->assertViewHas('canEdit');
});

it('shows create wish page', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create(['year' => now()->year, 'is_active' => true]);

    $response = $this->actingAs($user)->get(route('wishes.create'));

    $response->assertStatus(200)
        ->assertViewIs('wishes.create')
        ->assertViewHas('activeTheme');
});

it('can store a new wish', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create(['year' => now()->year, 'is_active' => true]);

    $response = $this->actingAs($user)
        ->post(route('wishes.store'), [
            'content' => 'Test spiritual intention',
            'position' => 1,
        ]);

    $response->assertRedirect(route('wishes.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('wishes', [
        'user_id' => $user->id,
        'content' => 'Test spiritual intention',
        'position' => 1,
        'theme_id' => $theme->id,
    ]);
});

it('validates required fields when storing', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('wishes.store'), [
            'content' => '',
            'position' => '',
        ]);

    $response->assertSessionHasErrors(['content', 'position']);
});

it('can show individual wish', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme->id,
    ]);

    $response = $this->actingAs($user)->get(route('wishes.show', $wish));

    $response->assertStatus(200)
        ->assertViewIs('wishes.show')
        ->assertViewHas('wish', $wish);
});

it('cannot show other users wishes', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $theme = Theme::factory()->create();
    
    $wish = Wish::factory()->create([
        'user_id' => $user2->id,
        'theme_id' => $theme->id,
    ]);

    $response = $this->actingAs($user1)->get(route('wishes.show', $wish));

    $response->assertStatus(403);
});

it('can update own wish', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme->id,
        'content' => 'Original wish',
    ]);

    $response = $this->actingAs($user)
        ->put(route('wishes.update', $wish), [
            'content' => 'Updated spiritual intention',
            'position' => $wish->position,
        ]);

    $response->assertRedirect(route('wishes.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('wishes', [
        'id' => $wish->id,
        'content' => 'Updated spiritual intention',
    ]);
});

it('can delete own wish', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('wishes.destroy', $wish));

    $response->assertRedirect(route('wishes.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('wishes', [
        'id' => $wish->id,
    ]);
});

it('can reorder wishes', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $theme = Theme::factory()->create();
    
    // Mock WishEditWindow to allow editing
    $this->partialMock(\App\Services\WishEditWindow::class, function ($mock) {
        $mock->shouldReceive('isOpen')->andReturn(true);
    });
    
    // Mock ThemeService to return our test theme
    $this->partialMock(\App\Services\ThemeService::class, function ($mock) use ($theme) {
        $mock->shouldReceive('getActiveTheme')->andReturn($theme);
        $mock->shouldReceive('getCurrentYearTheme')->andReturn($theme);
    });
    
    $wish1 = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id, 'position' => 1]);
    $wish2 = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id, 'position' => 2]);
    $wish3 = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id, 'position' => 3]);

    $response = $this->actingAs($user)
        ->patch(route('wishes.reorder'), [
            'wishes' => [$wish3->id, $wish1->id, $wish2->id]
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $wish1->refresh();
    $wish2->refresh();
    $wish3->refresh();

    expect($wish3->position)->toBe(1)
        ->and($wish1->position)->toBe(2)
        ->and($wish2->position)->toBe(3);
});

it('requires authentication for all routes', function () {
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create(['theme_id' => $theme->id]);

    $this->get(route('wishes.index'))->assertRedirect('/login');
    $this->get(route('wishes.create'))->assertRedirect('/login');
    $this->post(route('wishes.store'))->assertRedirect('/login');
    $this->get(route('wishes.show', $wish))->assertRedirect('/login');
    $this->get(route('wishes.edit', $wish))->assertRedirect('/login');
    $this->put(route('wishes.update', $wish))->assertRedirect('/login');
    $this->delete(route('wishes.destroy', $wish))->assertRedirect('/login');
    $this->patch(route('wishes.reorder'))->assertRedirect('/login');
});