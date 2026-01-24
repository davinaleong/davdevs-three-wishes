<?php

use App\Policies\WishPolicy;
use App\Models\User;
use App\Models\Wish;
use App\Models\Theme;

it('allows user to view their own wishes', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);
    
    $policy = new WishPolicy();
    
    expect($policy->view($user, $wish))->toBeTrue();
});

it('prevents user from viewing other users wishes', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create(['user_id' => $user2->id, 'theme_id' => $theme->id]);
    
    $policy = new WishPolicy();
    
    expect($policy->view($user1, $wish))->toBeFalse();
});

it('allows user to update their own wishes', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);
    
    $policy = new WishPolicy();
    
    expect($policy->update($user, $wish))->toBeTrue();
});

it('prevents user from updating other users wishes', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create(['user_id' => $user2->id, 'theme_id' => $theme->id]);
    
    $policy = new WishPolicy();
    
    expect($policy->update($user1, $wish))->toBeFalse();
});

it('allows user to delete their own wishes', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);
    
    $policy = new WishPolicy();
    
    expect($policy->delete($user, $wish))->toBeTrue();
});

it('prevents user from deleting other users wishes', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish = Wish::factory()->create(['user_id' => $user2->id, 'theme_id' => $theme->id]);
    
    $policy = new WishPolicy();
    
    expect($policy->delete($user1, $wish))->toBeFalse();
});