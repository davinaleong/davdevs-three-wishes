<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    public function index(): View
    {
        auth('admin')->user()->logActivity('ADMIN_THEMES_VIEWED');

        $themes = Theme::orderBy('year', 'desc')->get();
        return view('admin.themes.index', compact('themes'));
    }

    public function show(Theme $theme): View
    {
        auth('admin')->user()->logActivity('ADMIN_THEME_VIEWED', ['theme_id' => $theme->id]);

        return view('admin.themes.show', compact('theme'));
    }

    public function create(): View
    {
        return view('admin.themes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'year' => 'required|integer|unique:themes,year',
            'theme_title' => 'required|string|max:255',
            'theme_tagline' => 'nullable|string|max:255',
            'theme_verse_reference' => 'required|string|max:100',
            'theme_verse_text' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'colors_json' => ['required', 'string', function ($attribute, $value, $fail) {
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail('Theme colors must be a JSON object (e.g. {"text":"#000000"}).');
                }
            }],
            'email_styles_json' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value === null || trim($value) === '') return;
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail('Email styles must be a JSON object.');
                }
            }],
        ]);

        $data = $request->all();
        $data['colors_json'] = $this->decodeJsonObjectStringToArray($request->input('colors_json')) ?? [];
        $data['email_styles_json'] = $this->decodeJsonObjectStringToArray($request->input('email_styles_json'));
        
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('themes/logos', 'public');
        }
        
        if ($request->hasFile('favicon')) {
            $data['favicon_path'] = $request->file('favicon')->store('themes/favicons', 'public');
        }

        $theme = Theme::create($data);

        auth('admin')->user()->logActivity('ADMIN_THEME_CREATED', ['theme_id' => $theme->id]);

        return redirect()->route('admin.themes.show', $theme)
            ->with('success', 'Theme created successfully.');
    }

    public function edit(Theme $theme): View
    {
        //dd($theme->colors_json);
        return view('admin.themes.edit', compact('theme'));
    }

    public function update(Request $request, Theme $theme): RedirectResponse
    {
        $request->validate([
            'year' => 'required|integer|unique:themes,year,' . $theme->id,
            'theme_title' => 'required|string|max:255',
            'theme_tagline' => 'nullable|string|max:255',
            'theme_verse_reference' => 'required|string|max:100',
            'theme_verse_text' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'colors_json' => ['required', 'string', function ($attribute, $value, $fail) {
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail('Theme colors must be a JSON object (e.g. {"text":"#000000"}).');
                }
            }],
            'email_styles_json' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value === null || trim($value) === '') return;
                $decoded = json_decode($value, true);
                if (!is_array($decoded)) {
                    $fail('Email styles must be a JSON object.');
                }
            }],

        ]);

        $data = $request->except(['logo', 'favicon']);
        $data['colors_json'] = $this->decodeJsonObjectStringToArray($request->input('colors_json')) ?? [];
        $data['email_styles_json'] = $this->decodeJsonObjectStringToArray($request->input('email_styles_json'));
        
        if ($request->hasFile('logo')) {
            if ($theme->logo_path) {
                Storage::disk('public')->delete($theme->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('themes/logos', 'public');
        }
        
        if ($request->hasFile('favicon')) {
            if ($theme->favicon_path) {
                Storage::disk('public')->delete($theme->favicon_path);
            }
            $data['favicon_path'] = $request->file('favicon')->store('themes/favicons', 'public');
        }

        $theme->update($data);

        auth('admin')->user()->logActivity('ADMIN_THEME_UPDATED', ['theme_id' => $theme->id]);

        return redirect()->route('admin.themes.show', $theme)
            ->with('success', 'Theme updated successfully.');
    }

    public function activate(Request $request, Theme $theme): RedirectResponse
    {
        // Deactivate all themes first
        Theme::query()->update(['is_active' => false]);
        
        // Activate the selected theme
        $theme->update(['is_active' => true]);

        auth('admin')->user()->logActivity('ADMIN_THEME_ACTIVATED', ['theme_id' => $theme->id]);

        return back()->with('success', 'Theme activated successfully.');
    }

    public function destroy(Theme $theme): RedirectResponse
    {
        // Force delete - remove all restrictions
        
        // If deleting the active theme, deactivate it first
        if ($theme->is_active) {
            // Find another theme to activate or just deactivate
            $otherTheme = Theme::where('id', '!=', $theme->id)->first();
            if ($otherTheme) {
                Theme::where('is_active', true)->update(['is_active' => false]);
                $otherTheme->update(['is_active' => true]);
            } else {
                $theme->update(['is_active' => false]);
            }
        }

        // Delete associated wishes if any exist
        if ($theme->wishes()->exists()) {
            $theme->wishes()->delete();
        }

        // Delete associated files
        if ($theme->logo_path) {
            Storage::disk('public')->delete($theme->logo_path);
        }
        if ($theme->favicon_path) {
            Storage::disk('public')->delete($theme->favicon_path);
        }

        auth('admin')->user()->logActivity('ADMIN_THEME_FORCE_DELETED', [
            'theme_id' => $theme->id,
            'theme_title' => $theme->theme_title,
            'year' => $theme->year,
            'was_active' => $theme->is_active,
            'had_wishes' => $theme->wishes()->exists()
        ]);

        $theme->delete();

        return redirect()->route('admin.themes.index')
            ->with('success', 'Theme deleted successfully (including all associated data).');
    }

    private function decodeJsonObjectStringToArray(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        // If the decoded value is a string, it was double-encoded JSON. Decode again.
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : null;
    }

}