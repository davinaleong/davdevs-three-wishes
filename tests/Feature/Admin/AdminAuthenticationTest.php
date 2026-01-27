<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertViewIs('admin.auth.login');
    }

    public function test_admin_can_authenticate_using_login_screen()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin, 'admin');
        $response->assertRedirect('/admin');
    }

    public function test_admin_can_not_authenticate_with_invalid_password()
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('admin');
    }

    public function test_admin_can_logout()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->post('/admin/logout');

        $this->assertGuest('admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_dashboard_requires_authentication()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_can_access_dashboard()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    public function test_admin_login_logs_activity()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'action' => 'ADMIN_LOGIN',
        ]);
    }

    public function test_admin_logout_logs_activity()
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post('/admin/logout');

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'action' => 'ADMIN_LOGOUT',
        ]);
    }

    public function test_regular_users_cannot_access_admin_areas()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }
}