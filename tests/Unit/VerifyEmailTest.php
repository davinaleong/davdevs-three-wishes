<?php

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Theme;
use Illuminate\Support\Facades\Mail;

it('can create verify email', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $verificationUrl = 'https://example.com/verify/123';

    $email = new VerifyEmail($user, $verificationUrl);

    expect($email->user)->toBe($user)
        ->and($email->verificationUrl)->toBe($verificationUrl)
        ->and($email->year)->toBe(now()->year);
});

it('has correct subject line', function () {
    $user = User::factory()->create();
    $verificationUrl = 'https://example.com/verify/123';
    $email = new VerifyEmail($user, $verificationUrl);

    $envelope = $email->envelope();

    expect($envelope->subject)->toBe('🙏 Verify Your Email - Welcome to Dav/Devs Three Wishes ' . now()->year . '!');
});

it('uses correct view', function () {
    $user = User::factory()->create();
    $verificationUrl = 'https://example.com/verify/123';
    $email = new VerifyEmail($user, $verificationUrl);

    $content = $email->content();

    expect($content->view)->toBe('emails.verify-email');
});

it('is queued', function () {
    $user = User::factory()->create();
    $verificationUrl = 'https://example.com/verify/123';
    $email = new VerifyEmail($user, $verificationUrl);

    expect($email)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

it('can be sent', function () {
    Mail::fake();
    
    $user = User::factory()->create(['email' => 'test@example.com']);
    $verificationUrl = 'https://example.com/verify/123';
    $email = new VerifyEmail($user, $verificationUrl);

    Mail::to($user->email)->send($email);

    Mail::assertSent(VerifyEmail::class, function ($mail) use ($user) {
        return $mail->user->email === $user->email;
    });
});