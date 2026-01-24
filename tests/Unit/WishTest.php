<?php

use App\Models\User;
use App\Models\Theme;
use App\Models\Wish;

it('can create a wish with required attributes', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();

    $wish = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme->id,
        'content' => 'Test wish content',
        'position' => 1,
    ]);

    expect($wish->content)->toBe('Test wish content')
        ->and($wish->position)->toBe(1)
        ->and($wish->user_id)->toBe($user->id)
        ->and($wish->theme_id)->toBe($theme->id);
});

it('belongs to user and theme', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create([
        'user_id' => $user->id,
        'theme_id' => $theme->id,
    ]);

    expect($wish->user)->toBeInstanceOf(User::class)
        ->and($wish->user->id)->toBe($user->id)
        ->and($wish->theme)->toBeInstanceOf(Theme::class)
        ->and($wish->theme->id)->toBe($theme->id);
});

it('can scope for theme', function () {
    $theme1 = Theme::factory()->create();
    $theme2 = Theme::factory()->create();
    $user = User::factory()->create();

    $wish1 = Wish::factory()->create(['theme_id' => $theme1->id, 'user_id' => $user->id]);
    $wish2 = Wish::factory()->create(['theme_id' => $theme2->id, 'user_id' => $user->id]);

    $wishesForTheme1 = Wish::forTheme($theme1->id)->get();

    expect($wishesForTheme1)->toHaveCount(1)
        ->and($wishesForTheme1->first()->id)->toBe($wish1->id);
});

it('can scope ordered wishes', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();

    $wish3 = Wish::factory()->create(['position' => 3, 'user_id' => $user->id, 'theme_id' => $theme->id]);
    $wish1 = Wish::factory()->create(['position' => 1, 'user_id' => $user->id, 'theme_id' => $theme->id]);
    $wish2 = Wish::factory()->create(['position' => 2, 'user_id' => $user->id, 'theme_id' => $theme->id]);

    $orderedWishes = Wish::ordered()->get();

    expect($orderedWishes->first()->position)->toBe(1)
        ->and($orderedWishes->last()->position)->toBe(3);
});