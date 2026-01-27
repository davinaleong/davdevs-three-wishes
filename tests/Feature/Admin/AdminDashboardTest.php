<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Theme;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    public function test_admin_dashboard_displays_user_statistics()
    {
        // Create test data
        User::factory(5)->create();
        User::factory(3)->verified()->create();
        User::factory(2)->create(['two_factor_enabled_at' => now()]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('8'); // total users
        $response->assertSee('3'); // verified users
        $response->assertSee('2'); // 2FA users
    }

    public function test_admin_dashboard_displays_wish_statistics()
    {
        $user = User::factory()->verified()->create();
        $theme = Theme::factory()->create();
        Wish::factory(3)->create(['user_id' => $user->id, 'theme_id' => $theme->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('averageWishesPerUser');
        $response->assertSee('3'); // total wishes
    }

    public function test_admin_dashboard_displays_active_theme()
    {
        $activeTheme = Theme::factory()->create([
            'theme_title' => 'Test Theme 2026',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('activeTheme');
        $response->assertSee('Test Theme 2026');
    }

    public function test_admin_dashboard_shows_recent_activity()
    {
        $user = User::factory()->create(['name' => 'Test User']);
        $user->logActivity('WISH_CREATED', ['test' => 'data']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('recentUserActivity');
        $response->assertSee('Test User');
    }

    public function test_admin_dashboard_logs_access()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        
        // Verify that dashboard access was logged
        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_DASHBOARD_VIEWED',
        ]);
    }

    public function test_admin_dashboard_has_quick_action_links()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee(route('admin.themes.create'));
        $response->assertSee(route('admin.emails.index'));
    }

    public function test_dashboard_shows_no_recent_activity_message_when_empty()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('No recent activity');
    }
}