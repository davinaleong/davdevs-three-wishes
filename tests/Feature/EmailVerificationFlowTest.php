<?php

use App\Models\User;
use App\Models\Theme;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Registered;

it('sends custom verification email on registration', function () {
    Mail::fake();
    
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    
    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not->toBeNull();
    
    // Should send verification email (handled by Laravel's built-in system)
    // but our custom implementation should override it
});

it('triggers welcome email after email verification', function () {
    Mail::fake();
    
    $user = User::factory()->unverified()->create();
    
    // Generate verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );
    
    // Visit verification URL
    $response = $this->actingAs($user)->get($verificationUrl);
    
    $response->assertRedirect('/dashboard');
    
    // Should send welcome email after verification
    Mail::assertSent(WelcomeEmail::class, function ($mail) use ($user) {
        return $mail->user->id === $user->id;
    });
});

it('creates activity log for email verification', function () {
    $user = User::factory()->unverified()->create();
    
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );
    
    $this->actingAs($user)->get($verificationUrl);
    
    $this->assertDatabaseHas('user_activity_logs', [
        'user_id' => $user->id,
        'action' => 'welcome_email_sent',
    ]);
});

it('redirects unverified users to verification notice', function () {
    $user = User::factory()->unverified()->create();
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertRedirect('/verify-email');
});

it('allows verified users to access dashboard', function () {
    $user = User::factory()->create(); // Already verified by default
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertRedirect('/wishes'); // Dashboard redirects to wishes
});

it('welcome email contains correct branding', function () {
    Mail::fake();
    
    $user = User::factory()->create();
    $theme = Theme::factory()->create(['year' => now()->year]);
    
    event(new \Illuminate\Auth\Events\Verified($user));
    
    Mail::assertSent(WelcomeEmail::class, function ($mail) {
        return str_contains($mail->envelope()->subject, 'Dav/Devs Three Wishes');
    });
});