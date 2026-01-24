<?php

use App\Console\Commands\SendAnnualWishEmails;
use App\Mail\AnnualWishReminder;
use App\Models\User;
use App\Models\Theme;
use App\Models\Wish;
use Illuminate\Support\Facades\Mail;

it('sends annual emails to all users', function () {
    Mail::fake();
    
    $theme = Theme::factory()->create(['year' => now()->year]);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    Wish::factory()->create(['user_id' => $user1->id, 'theme_id' => $theme->id]);
    Wish::factory()->create(['user_id' => $user2->id, 'theme_id' => $theme->id]);

    $this->artisan('wishes:send-annual-emails')
        ->assertExitCode(0);

    Mail::assertQueued(AnnualWishReminder::class, 2);
});

it('can send to specific user only', function () {
    Mail::fake();
    
    $theme = Theme::factory()->create(['year' => now()->year]);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    Wish::factory()->create(['user_id' => $user1->id, 'theme_id' => $theme->id]);
    Wish::factory()->create(['user_id' => $user2->id, 'theme_id' => $theme->id]);

    $this->artisan('wishes:send-annual-emails', ['--user' => $user1->uuid])
        ->assertExitCode(0);

    Mail::assertQueued(AnnualWishReminder::class, 1);
    Mail::assertQueued(AnnualWishReminder::class, function ($mail) use ($user1) {
        return $mail->user->id === $user1->id;
    });
});

it('dry run mode does not send emails', function () {
    Mail::fake();
    
    $theme = Theme::factory()->create(['year' => now()->year]);
    $user = User::factory()->create();
    Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);

    $this->artisan('wishes:send-annual-emails', ['--dry-run' => true])
        ->assertExitCode(0);

    Mail::assertNothingQueued();
});

it('logs activity when emails are sent', function () {
    Mail::fake();
    
    $theme = Theme::factory()->create(['year' => now()->year]);
    $user = User::factory()->create();
    Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);

    $this->artisan('wishes:send-annual-emails')
        ->assertExitCode(0);

    $this->assertDatabaseHas('user_activity_logs', [
        'user_id' => $user->id,
        'action' => 'annual_wish_email_sent',
    ]);
});

it('skips users without wishes', function () {
    Mail::fake();
    
    $userWithWishes = User::factory()->create();
    $userWithoutWishes = User::factory()->create();
    
    $theme = Theme::factory()->create(['year' => now()->year]);
    Wish::factory()->create(['user_id' => $userWithWishes->id, 'theme_id' => $theme->id]);

    $this->artisan('wishes:send-annual-emails')
        ->assertExitCode(0);

    Mail::assertQueued(AnnualWishReminder::class, 1);
    Mail::assertQueued(AnnualWishReminder::class, function ($mail) use ($userWithWishes) {
        return $mail->user->id === $userWithWishes->id;
    });
});

it('handles invalid user id gracefully', function () {
    Mail::fake();
    
    $this->artisan('wishes:send-annual-emails', ['--user' => 'invalid-uuid'])
        ->assertExitCode(0); // Command succeeds but sends no emails
        
    Mail::assertNothingQueued();
});