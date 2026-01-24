<?php

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Models\Theme;
use Illuminate\Support\Facades\Mail;

it('can create welcome email', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $theme = Theme::factory()->create(['year' => now()->year]);

    $email = new WelcomeEmail($user);

    expect($email->user)->toBe($user)
        ->and($email->year)->toBe(now()->year)
        ->and($email->yearTheme)->toBeInstanceOf(Theme::class);
});

it('has correct subject line', function () {
    $user = User::factory()->create();
    $email = new WelcomeEmail($user);

    $envelope = $email->envelope();

    expect($envelope->subject)->toBe('Welcome to Dav/Devs Three Wishes! Your spiritual journey begins here ✨');
});

it('uses correct view', function () {
    $user = User::factory()->create();
    $email = new WelcomeEmail($user);

    $content = $email->content();

    expect($content->view)->toBe('emails.welcome');
});

it('is queued', function () {
    $email = new WelcomeEmail(User::factory()->create());

    expect($email)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

it('can be sent', function () {
    Mail::fake();
    
    $user = User::factory()->create(['email' => 'test@example.com']);
    $email = new WelcomeEmail($user);

    Mail::to($user->email)->send($email);

    Mail::assertSent(WelcomeEmail::class, function ($mail) use ($user) {
        return $mail->user->email === $user->email;
    });
});