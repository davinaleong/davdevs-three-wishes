<?php

namespace Tests\Feature\Admin;

use App\Mail\AnnualWishReminder;
use App\Mail\VerifyEmail;
use App\Models\Admin;
use App\Models\Theme;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEmailManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        Mail::fake();
    }

    public function test_admin_can_view_email_tools()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/emails');

        $response->assertStatus(200);
        $response->assertViewIs('admin.emails.index');
    }

    public function test_admin_can_send_broadcast_email_to_verified_users()
    {
        // Create test users
        User::factory(3)->verified()->create();
        User::factory(2)->create(); // Unverified users

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                'subject' => 'Test Broadcast',
                'content' => 'This is a test message',
                'target' => 'verified',
            ]);

        // Should send to 3 verified users only
        // Since we're using Mail::raw, we can't easily assert the mail was sent
        // but we can verify the request was successful and activity was logged

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_BROADCAST_EMAIL_SENT',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_send_broadcast_email_to_all_users()
    {
        User::factory(5)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                'subject' => 'Test All Users',
                'content' => 'Message to all users',
                'target' => 'all',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_send_broadcast_email_to_2fa_users()
    {
        User::factory(3)->create(['two_factor_enabled_at' => now()]);
        User::factory(2)->create(); // Non-2FA users

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                'subject' => 'Security Update',
                'content' => 'Message for 2FA users',
                'target' => '2fa',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_broadcast_email_fails_with_no_matching_users()
    {
        // No 2FA users exist
        User::factory(2)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                'subject' => 'Test',
                'content' => 'Test message',
                'target' => '2fa',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No users match the selected criteria.');
    }

    public function test_broadcast_email_limits_recipients()
    {
        // Create more than 100 users
        User::factory(101)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                'subject' => 'Test',
                'content' => 'Test message',
                'target' => 'all',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Too many recipients. Please refine your target audience.');
    }

    public function test_admin_can_send_year_end_batch()
    {
        $theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        $users = User::factory(3)->verified()->create();

        // Create wishes for each user
        foreach ($users as $user) {
            Wish::factory(2)->create([
                'user_id' => $user->id,
                'theme_id' => $theme->id,
            ]);
        }

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/year-end-batch', [
                'year' => 2026,
                'limit' => 50,
            ]);

        Mail::assertQueued(AnnualWishReminder::class, 3);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_YEAR_END_BATCH_SENT',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_year_end_batch_requires_valid_year()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/year-end-batch', [
                'year' => 9999, // Non-existent year
                'limit' => 10,
            ]);

        $response->assertSessionHasErrors(['year']);
    }

    public function test_broadcast_email_validates_required_fields()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                // Missing required fields
            ]);

        $response->assertSessionHasErrors([
            'subject',
            'content', 
            'target',
        ]);
    }

    public function test_broadcast_email_validates_target_options()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                'subject' => 'Test',
                'content' => 'Test message',
                'target' => 'invalid_target',
            ]);

        $response->assertSessionHasErrors(['target']);
    }

    public function test_email_tools_access_logs_activity()
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/emails');

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_EMAIL_TOOLS_VIEWED',
        ]);
    }
}