<?php

namespace Tests\Unit\Admin;

use App\Models\Admin;
use App\Models\AdminActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_factory_creates_valid_admin()
    {
        $admin = Admin::factory()->create();

        $this->assertNotNull($admin->uuid);
        $this->assertTrue(Str::isUuid((string) $admin->uuid));
        $this->assertNotNull($admin->name);
        $this->assertNotNull($admin->email);
        $this->assertFalse($admin->is_super_admin);
    }

    public function test_admin_factory_can_create_super_admin()
    {
        $admin = Admin::factory()->superAdmin()->create();

        $this->assertTrue($admin->is_super_admin);
    }

    public function test_admin_password_is_hashed()
    {
        $admin = Admin::factory()->create(['password' => 'plain-password']);

        $this->assertNotEquals('plain-password', $admin->password);
        $this->assertTrue(password_verify('plain-password', $admin->password));
    }

    public function test_admin_uuid_is_automatically_generated()
    {
        $admin = Admin::factory()->make();
        $this->assertNull($admin->uuid);

        $admin->save();
        $this->assertNotNull($admin->uuid);
        $this->assertTrue(Str::isUuid((string) $admin->uuid));
    }

    public function test_admin_route_key_is_uuid()
    {
        $admin = Admin::factory()->create();

        $this->assertEquals('uuid', $admin->getRouteKeyName());
        $this->assertEquals($admin->uuid, $admin->getRouteKey());
    }

    public function test_admin_has_activity_logs_relationship()
    {
        $admin = Admin::factory()->create();
        AdminActivityLog::factory(3)->create(['admin_id' => $admin->id]);

        $this->assertEquals(3, $admin->activityLogs()->count());
    }

    public function test_admin_can_check_two_factor_status()
    {
        $adminWithoutTwoFactor = Admin::factory()->create();
        $adminWithTwoFactor = Admin::factory()->create(['two_factor_enabled_at' => now()]);

        $this->assertFalse($adminWithoutTwoFactor->hasTwoFactorEnabled());
        $this->assertTrue($adminWithTwoFactor->hasTwoFactorEnabled());
    }

    public function test_admin_can_log_activity()
    {
        $admin = Admin::factory()->create();

        $admin->logActivity('TEST_ACTION', ['test_data' => 'value']);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'action' => 'TEST_ACTION',
        ]);

        $log = AdminActivityLog::where('admin_id', $admin->id)->first();
        $this->assertEquals('value', $log->meta['test_data']);
    }

    public function test_admin_sensitive_attributes_are_hidden()
    {
        $admin = Admin::factory()->create([
            'password' => 'secret',
            'two_factor_secret' => 'secret_key',
            'two_factor_recovery_codes' => ['code1', 'code2'],
        ]);

        $array = $admin->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayNotHasKey('two_factor_secret', $array);
        $this->assertArrayNotHasKey('two_factor_recovery_codes', $array);
    }

    public function test_admin_casts_attributes_correctly()
    {
        $admin = Admin::factory()->create([
            'is_super_admin' => 1,
            'two_factor_enabled_at' => '2023-01-01 12:00:00',
            'two_factor_recovery_codes' => ['code1', 'code2'],
        ]);

        $this->assertIsBool($admin->is_super_admin);
        $this->assertTrue($admin->is_super_admin);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $admin->two_factor_enabled_at);
        $this->assertIsArray($admin->two_factor_recovery_codes);
    }

    public function test_admin_fillable_attributes()
    {
        $admin = new Admin();

        $expectedFillable = [
            'uuid',
            'name',
            'email',
            'password',
            'is_super_admin',
            'two_factor_enabled_at',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ];

        $this->assertEquals($expectedFillable, $admin->getFillable());
    }
}