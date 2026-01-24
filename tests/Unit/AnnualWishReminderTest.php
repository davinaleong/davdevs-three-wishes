<?php

use App\Mail\AnnualWishReminder;
use App\Models\User;
use App\Models\Theme;
use App\Models\Wish;
use Illuminate\Support\Facades\Mail;

it('can create annual wish reminder email', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $theme = Theme::factory()->create(['year' => now()->year]);
    $wish = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);

    $email = new AnnualWishReminder($user);

    expect($email->user)->toBe($user)
        ->and($email->year)->toBe(now()->year)
        ->and($email->wishes)->toHaveCount(1);
});

it('has correct subject line', function () {
    $user = User::factory()->create();
    $email = new AnnualWishReminder($user);

    $envelope = $email->envelope();

    expect($envelope->subject)->toBe("Your {$email->year} Dav/Devs Three Wishes - God's Faithfulness & New Hopes!");
});

it('uses correct view', function () {
    $user = User::factory()->create();
    $email = new AnnualWishReminder($user);

    $content = $email->content();

    expect($content->view)->toBe('emails.annual-wish-reminder');
});

it('is queued', function () {
    $user = User::factory()->create();
    $email = new AnnualWishReminder($user);

    expect($email)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

it('includes user wishes with theme', function () {
    $user = User::factory()->create();
    $theme = Theme::factory()->create();
    $wish1 = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);
    $wish2 = Wish::factory()->create(['user_id' => $user->id, 'theme_id' => $theme->id]);

    $email = new AnnualWishReminder($user);

    expect($email->wishes)->toHaveCount(2);
    expect($email->wishes->first()->theme)->toBeInstanceOf(Theme::class);
});

it('can be sent', function () {
    Mail::fake();
    
    $user = User::factory()->create(['email' => 'test@example.com']);
    $email = new AnnualWishReminder($user);

    Mail::to($user->email)->send($email);

    Mail::assertSent(AnnualWishReminder::class, function ($mail) use ($user) {
        return $mail->user->email === $user->email;
    });
});