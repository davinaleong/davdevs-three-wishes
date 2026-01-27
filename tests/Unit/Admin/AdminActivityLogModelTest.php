<?php

namespace Tests\Unit\Admin;

use App\Models\Admin;
use App\Models\AdminActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityLogModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_activity_log_factory_creates_valid_log()
    {
        $log = AdminActivityLog::factory()->create();

        $this->assertNotNull($log->admin_id);
        $this->assertNotNull($log->action);
        $this->assertNotNull($log->created_at);
        $this->assertIsArray($log->meta);
    }

    public function test_admin_activity_log_belongs_to_admin()
    {
        $admin = Admin::factory()->create();
        $log = AdminActivityLog::factory()->create(['admin_id' => $admin->id]);

        $this->assertEquals($admin->id, $log->admin->id);
        $this->assertEquals($admin->name, $log->admin->name);
    }

    public function test_admin_activity_log_casts_meta_as_array()
    {
        $meta = ['ip' => '127.0.0.1', 'user_agent' => 'Test Browser'];
        $log = AdminActivityLog::factory()->create(['meta' => $meta]);

        $this->assertIsArray($log->meta);
        $this->assertEquals('127.0.0.1', $log->meta['ip']);
        $this->assertEquals('Test Browser', $log->meta['user_agent']);
    }

    public function test_admin_activity_log_has_no_updated_at_timestamp()
    {
        $log = new AdminActivityLog();

        $this->assertNull($log::UPDATED_AT);
    }

    public function test_admin_activity_log_static_log_method()
    {
        $admin = Admin::factory()->create();
        
        $log = AdminActivityLog::log($admin->id, 'TEST_ACTION', ['custom' => 'data']);

        $this->assertInstanceOf(AdminActivityLog::class, $log);
        $this->assertEquals($admin->id, $log->admin_id);
        $this->assertEquals('TEST_ACTION', $log->action);
        $this->assertArrayHasKey('custom', $log->meta);
        $this->assertArrayHasKey('ip', $log->meta);
        $this->assertArrayHasKey('user_agent', $log->meta);
    }

    public function test_admin_activity_log_automatically_includes_request_data()
    {
        $admin = Admin::factory()->create();

        $log = AdminActivityLog::log($admin->id, 'TEST_ACTION');

        $this->assertArrayHasKey('ip', $log->meta);
        $this->assertArrayHasKey('user_agent', $log->meta);
        $this->assertIsString($log->meta['ip']);
        $this->assertIsString($log->meta['user_agent']);
    }

    public function test_admin_activity_log_fillable_attributes()
    {
        $log = new AdminActivityLog();

        $expectedFillable = [
            'admin_id',
            'action',
            'meta',
        ];

        $this->assertEquals($expectedFillable, $log->getFillable());
    }

    public function test_admin_activity_log_created_at_is_datetime()
    {
        $log = AdminActivityLog::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $log->created_at);
    }
}