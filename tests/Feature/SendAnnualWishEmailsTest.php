<?php

use Illuminate\Support\Facades\Mail;

it('annual email command is disabled for privacy compliance', function () {
    Mail::fake();
    
    $this->artisan('wishes:send-annual-emails')
        ->expectsOutput('Annual wish email functionality has been disabled for privacy compliance.')
        ->assertExitCode(1);

    // No emails should be sent
    Mail::assertNothingQueued();
});

it('annual email command with options still disabled', function () {
    Mail::fake();
    
    $this->artisan('wishes:send-annual-emails', ['--dry-run' => true])
        ->expectsOutput('Annual wish email functionality has been disabled for privacy compliance.')
        ->assertExitCode(1);

    Mail::assertNothingQueued();
});

it('annual email command with user parameter still disabled', function () {
    Mail::fake();
    
    $this->artisan('wishes:send-annual-emails', ['--user' => 'any-uuid'])
        ->expectsOutput('Annual wish email functionality has been disabled for privacy compliance.')
        ->assertExitCode(1);

    Mail::assertNothingQueued();
});