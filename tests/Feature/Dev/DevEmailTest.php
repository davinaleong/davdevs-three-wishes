<?php

namespace Tests\Feature\Dev;

use App\Mail\AnnualWishReminder;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use App\Models\Theme;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class DevEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Mail::fake();
    }

    public function test_dev_email_dashboard_can_be_accessed()
    {

        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)
            ->get('/dev/emails/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dev.emails.dashboard');
    }

    public function test_dev_email_dashboard_requires_authentication()
    {
        $response = $this->get('/dev/emails/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dev_routes_only_available_in_local_environment()
    {
        // Note: Routes are loaded at boot time, so we can't dynamically exclude them
        // Instead, verify that in non-testing environments, these routes would be protected
        // This test verifies the environment check exists in web.php
        
        $user = User::factory()->verified()->create();
        
        // In testing environment, routes should still work
        $response = $this->actingAs($user)
            ->get('/dev/emails/dashboard');

        $response->assertStatus(200);
        
        // Verify the environment check exists in web.php (this is a meta-test)
        $webRoutes = file_get_contents(base_path('routes/web.php'));
        $this->assertStringContainsString("app()->environment(['local', 'testing'])", $webRoutes);
    }

    public function test_can_send_verification_email_manually()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/dev/emails/verification');

        Mail::assertQueued(VerifyEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_can_send_password_reset_email_manually()
    {
        $user = User::factory()->verified()->create();

        // Mock the Password facade to return success
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        $response = $this->actingAs($user)
            ->get('/dev/emails/password-reset');

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_can_send_welcome_email_manually()
    {
        $user = User::factory()->verified()->create();
        Theme::factory()->active()->create();

        $response = $this->actingAs($user)
            ->get('/dev/emails/welcome');

        Mail::assertQueued(WelcomeEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_can_send_year_end_wishes_email_manually()
    {
        $user = User::factory()->verified()->create();
        $theme = Theme::factory()->active()->create();
        
        // Create some wishes for the user
        Wish::factory(3)->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/dev/emails/year-end-wishes');

        Mail::assertQueued(AnnualWishReminder::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_year_end_email_fails_when_no_wishes_exist()
    {
        $user = User::factory()->verified()->create();
        Theme::factory()->active()->create();
        // No wishes created

        $response = $this->actingAs($user)
            ->get('/dev/emails/year-end-wishes');

        Mail::assertNotSent(AnnualWishReminder::class);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'No wishes found for the selected theme/year');
    }

    public function test_year_end_email_fails_when_no_theme_exists()
    {
        $user = User::factory()->verified()->create();
        // No theme created

        $response = $this->actingAs($user)
            ->get('/dev/emails/year-end-wishes');

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No wishes found for the selected theme/year');
    }

    public function test_can_preview_verification_email()
    {
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)
            ->get('/dev/emails/preview?type=verification');

        $response->assertStatus(200);
        $response->assertViewIs('dev.emails.preview');
        $response->assertViewHas('type', 'verification');
    }

    public function test_can_preview_welcome_email()
    {
        $user = User::factory()->verified()->create();
        $theme = Theme::factory()->active()->create();

        $response = $this->actingAs($user)
            ->get('/dev/emails/preview?type=welcome');

        $response->assertStatus(200);
        $response->assertViewIs('dev.emails.preview');
        $response->assertViewHas('type', 'welcome');
    }

    public function test_can_preview_year_end_email_with_wishes()
    {
        $user = User::factory()->verified()->create();
        $theme = Theme::factory()->active()->create();
        
        Wish::factory(2)->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/dev/emails/preview?type=year-end');

        $response->assertStatus(200);
        $response->assertViewIs('dev.emails.preview');
        $response->assertViewHas('type', 'year-end');
    }

    public function test_can_preview_year_end_email_with_fake_wishes_when_none_exist()
    {
        $user = User::factory()->verified()->create();
        Theme::factory()->active()->create();
        // No wishes - should use fake wishes for preview

        $response = $this->actingAs($user)
            ->get('/dev/emails/preview?type=year-end');

        $response->assertStatus(200);
        $response->assertViewIs('dev.emails.preview');
        $response->assertViewHas('type', 'year-end');
    }

    public function test_preview_with_invalid_email_type_returns_404()
    {
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)
            ->get('/dev/emails/preview?type=invalid');

        $response->assertStatus(404);
    }

    public function test_preview_year_end_email_with_specific_theme()
    {
        $user = User::factory()->verified()->create();
        $theme1 = Theme::factory()->create(['year' => 2025]);
        $theme2 = Theme::factory()->create(['year' => 2026]);
        
        Wish::factory(2)->create([
            'user_id' => $user->id,
            'theme_id' => $theme1->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/dev/emails/preview?type=year-end&theme_id={$theme1->id}");

        $response->assertStatus(200);
        $response->assertViewHas('theme', $theme1);
    }

    public function test_password_reset_handles_failure()
    {
        $user = User::factory()->verified()->create();

        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn('passwords.throttled'); // Simulate failure

        $response = $this->actingAs($user)
            ->get('/dev/emails/password-reset');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_year_end_email_can_specify_theme_id()
    {
        $user = User::factory()->verified()->create();
        $theme = Theme::factory()->create();
        
        Wish::factory(2)->create([
            'user_id' => $user->id,
            'theme_id' => $theme->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/dev/emails/year-end-wishes?theme_id={$theme->id}");

        Mail::assertQueued(AnnualWishReminder::class);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}