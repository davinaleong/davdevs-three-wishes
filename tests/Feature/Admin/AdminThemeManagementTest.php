<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminThemeManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        Storage::fake('public');
    }

    public function test_admin_can_view_themes_index()
    {
        Theme::factory(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/themes');

        $response->assertStatus(200);
        $response->assertViewIs('admin.themes.index');
        $response->assertViewHas('themes');
    }

    public function test_admin_can_view_single_theme()
    {
        $theme = Theme::factory()->create(['theme_title' => 'Test Theme']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get("/admin/themes/{$theme->uuid}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.themes.show');
        $response->assertSee('Test Theme');
    }

    public function test_admin_can_create_new_theme()
    {
        $logo = UploadedFile::fake()->image('logo.png', 100, 100);
        $favicon = UploadedFile::fake()->image('favicon.png', 32, 32); // Changed from .ico to .png

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/themes', [
                'year' => 2099, // Use a unique year to avoid conflicts
                'theme_title' => 'New Theme 2099',
                'theme_tagline' => 'A test theme',
                'theme_verse_reference' => 'John 3:16',
                'theme_verse_text' => 'For God so loved the world...',
                'logo' => $logo,
                'favicon' => $favicon,
                'colors_json' => json_encode(['primary' => '#3B82F6']),
                'email_styles_json' => json_encode(['header' => '#1E40AF']),
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('themes', [
            'year' => 2099,
            'theme_title' => 'New Theme 2099',
        ]);

        // Check files were uploaded
        Storage::disk('public')->assertExists('themes/logos/' . $logo->hashName());
        Storage::disk('public')->assertExists('themes/favicons/' . $favicon->hashName());

        // Check activity log
        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_THEME_CREATED',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_update_existing_theme()
    {
        $theme = Theme::factory()->create([
            'theme_title' => 'Old Theme',
            'year' => 2026,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put("/admin/themes/{$theme->uuid}", [
                'year' => 2026,
                'theme_title' => 'Updated Theme',
                'theme_tagline' => 'Updated tagline',
                'theme_verse_reference' => 'Romans 8:28',
                'theme_verse_text' => 'And we know that in all things...',
                'colors_json' => json_encode(['primary' => '#10B981']),
            ]);

        $this->assertDatabaseHas('themes', [
            'id' => $theme->id,
            'theme_title' => 'Updated Theme',
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_THEME_UPDATED',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_activate_theme()
    {
        $activeTheme = Theme::factory()->create(['is_active' => true]);
        $inactiveTheme = Theme::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post("/admin/themes/{$inactiveTheme->uuid}/activate");

        // Check that old theme is deactivated and new one is activated
        $this->assertDatabaseHas('themes', [
            'id' => $activeTheme->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('themes', [
            'id' => $inactiveTheme->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_THEME_ACTIVATED',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_cannot_delete_active_theme()
    {
        $theme = Theme::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete("/admin/themes/{$theme->uuid}");

        $this->assertDatabaseHas('themes', ['id' => $theme->id]);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cannot delete the active theme.');
    }

    public function test_admin_can_delete_inactive_theme_without_wishes()
    {
        $theme = Theme::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete("/admin/themes/{$theme->uuid}");

        $this->assertDatabaseMissing('themes', ['id' => $theme->id]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_THEME_DELETED',
        ]);

        $response->assertRedirect('/admin/themes');
        $response->assertSessionHas('success');
    }

    public function test_theme_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/themes', [
                'year' => 'invalid',
                'theme_title' => '', // Required field empty
                'theme_verse_reference' => '',
                'theme_verse_text' => '',
                'colors_json' => 'invalid json',
            ]);

        $response->assertSessionHasErrors([
            'year',
            'theme_title', 
            'theme_verse_reference',
            'theme_verse_text',
            'colors_json',
        ]);
    }

    public function test_theme_year_must_be_unique()
    {
        Theme::factory()->create(['year' => 2026]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/themes', [
                'year' => 2026, // Duplicate year
                'theme_title' => 'Another Theme',
                'theme_verse_reference' => 'John 3:16',
                'theme_verse_text' => 'For God so loved the world...',
                'colors_json' => json_encode(['primary' => '#3B82F6']),
            ]);

        $response->assertSessionHasErrors(['year']);
    }

    public function test_theme_viewing_logs_activity()
    {
        $theme = Theme::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->get("/admin/themes/{$theme->uuid}");

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_THEME_VIEWED',
        ]);
    }

    public function test_themes_index_viewing_logs_activity()
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/themes');

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'ADMIN_THEMES_VIEWED',
        ]);
    }
}