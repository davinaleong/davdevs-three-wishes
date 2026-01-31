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

    public function test_admin_dashboard_displays_wish_statistics_only()
    {
        // Create test data
        $user = User::factory()->verified()->create();
        $theme = Theme::factory()->create();
        Wish::factory(3)->create(['user_id' => $user->id, 'theme_id' => $theme->id]);
        Theme::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('3'); // total wishes
        $response->assertSee('1'); // active themes
        // User statistics should not be visible
        $response->assertDontSee('Total Users');
        $response->assertDontSee('Verified Users');
    }

    public function test_admin_dashboard_displays_theme_management_tools()
    {
        $theme = Theme::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('activeTheme');
        // Should not have user-specific averages
        $response->assertViewMissing('averageWishesPerUser');
        // Should have theme management links
        $response->assertSee('Create New Theme');
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

    public function test_admin_dashboard_shows_admin_activity_only()
    {
        // Log some admin activity
        $this->admin->logActivity('ADMIN_THEME_CREATED', ['theme_id' => 1]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('recentAdminActivity');
        // Should not have user activity
        $response->assertViewMissing('recentUserActivity');
        // Should not show user names
        $response->assertDontSee('Test User');
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

    public function test_dashboard_shows_admin_activity_when_available()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        // Should show admin activity (dashboard viewed) instead of empty message
        $response->assertSee('Admin dashboard viewed');
        $response->assertDontSee('No recent activity');
    }
}