<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Theme;
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

    public function test_admin_can_view_email_tools_with_disabled_message()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/emails');

        $response->assertStatus(200);
        $response->assertViewIs('admin.emails.index');
        $response->assertSee('Email Features Disabled');
        $response->assertSee('privacy compliance');
    }

    public function test_broadcast_email_routes_are_removed()
    {
        // These routes should no longer exist
        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/broadcast', [
                'subject' => 'Test Broadcast',
                'content' => 'This is a test message',
                'target' => 'verified',
            ]);

        $response->assertStatus(404);
    }

    public function test_year_end_batch_routes_are_removed()
    {
        $theme = Theme::factory()->create();
        
        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/emails/year-end-batch', [
                'year' => $theme->year,
                'limit' => 10,
            ]);

        $response->assertStatus(404);
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