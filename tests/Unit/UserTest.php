<?php

use App\Models\User;
use App\Models\Theme;
use Illuminate\Support\Str;

it('can create a user with uuid', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect((string) $user->uuid)->toBeString()
        ->and(Str::isUuid((string) $user->uuid))->toBeTrue()
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com');
});

it('can find user by uuid route key', function () {
    $user = User::factory()->create();
    
    expect($user->getRouteKeyName())->toBe('uuid');
    
    $foundUser = User::where('uuid', $user->uuid)->first();
    expect($foundUser->id)->toBe($user->id);
});

it('has correct relationships', function () {
    $user = User::factory()->create();
    
    expect($user->wishes())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->activityLogs())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('can check if two factor is enabled', function () {
    $user = User::factory()->create(['two_factor_enabled_at' => null]);
    expect($user->hasTwoFactorEnabled())->toBeFalse();
    
    $user->two_factor_enabled_at = now();
    expect($user->hasTwoFactorEnabled())->toBeTrue();
});

it('can log activity', function () {
    $user = User::factory()->create();
    
    $user->logActivity('test_action', ['key' => 'value']);
    
    $this->assertDatabaseHas('user_activity_logs', [
        'user_id' => $user->id,
        'action' => 'test_action',
    ]);
});

it('implements MustVerifyEmail', function () {
    $user = new User();
    expect($user)->toBeInstanceOf(\Illuminate\Contracts\Auth\MustVerifyEmail::class);
});