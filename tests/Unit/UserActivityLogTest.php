<?php

use App\Models\User;
use App\Models\UserActivityLog;

it('can create user activity log', function () {
    $user = User::factory()->create();
    
    $log = UserActivityLog::create([
        'user_id' => $user->id,
        'action' => 'test_action',
        'meta' => ['key' => 'value'],
    ]);

    expect($log->action)->toBe('test_action')
        ->and($log->meta)->toBe(['key' => 'value'])
        ->and($log->user_id)->toBe($user->id);
});

it('belongs to user', function () {
    $user = User::factory()->create();
    $log = UserActivityLog::create([
        'user_id' => $user->id,
        'action' => 'test_action',
    ]);

    expect($log->user)->toBeInstanceOf(User::class)
        ->and($log->user->id)->toBe($user->id);
});

it('can log activity using static method', function () {
    $user = User::factory()->create();
    
    UserActivityLog::log($user->id, 'static_test', ['data' => 'test']);
    
    $this->assertDatabaseHas('user_activity_logs', [
        'user_id' => $user->id,
        'action' => 'static_test',
    ]);

    $log = UserActivityLog::where('user_id', $user->id)
        ->where('action', 'static_test')
        ->first();
    
    expect($log->meta)->toHaveKey('data', 'test')
        ->and($log->meta)->toHaveKey('ip')
        ->and($log->meta)->toHaveKey('user_agent');
});

it('casts meta as array', function () {
    $user = User::factory()->create();
    
    $log = UserActivityLog::create([
        'user_id' => $user->id,
        'action' => 'test',
        'meta' => ['nested' => ['key' => 'value']],
    ]);

    expect($log->meta)->toBeArray()
        ->and($log->meta['nested']['key'])->toBe('value');
});