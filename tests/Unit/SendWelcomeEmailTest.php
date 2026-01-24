<?php

use App\Listeners\SendWelcomeEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

it('sends welcome email when user is verified', function () {
    Mail::fake();
    
    $user = User::factory()->create();
    $event = new Verified($user);
    $listener = new SendWelcomeEmail();

    $listener->handle($event);

    Mail::assertQueued(WelcomeEmail::class, function ($mail) use ($user) {
        return $mail->user->id === $user->id;
    });
});

it('logs activity when welcome email is sent', function () {
    Mail::fake();
    
    $user = User::factory()->create();
    $event = new Verified($user);
    $listener = new SendWelcomeEmail();

    $listener->handle($event);

    $this->assertDatabaseHas('user_activity_logs', [
        'user_id' => $user->id,
        'action' => 'welcome_email_sent',
    ]);
});

it('implements should queue interface', function () {
    $listener = new SendWelcomeEmail();
    
    expect($listener)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

it('uses interacts with queue trait', function () {
    $listener = new SendWelcomeEmail();
    
    expect(method_exists($listener, 'delete'))->toBeTrue()
        ->and(method_exists($listener, 'release'))->toBeTrue();
});

it('handles event with user email', function () {
    Mail::fake();
    
    $user = User::factory()->create(['email' => 'specific@example.com']);
    $event = new Verified($user);
    $listener = new SendWelcomeEmail();

    $listener->handle($event);

    Mail::assertQueued(WelcomeEmail::class, function ($mail) {
        return $mail->hasTo('specific@example.com');
    });
});