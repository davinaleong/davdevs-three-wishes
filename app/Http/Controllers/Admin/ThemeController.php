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
            'colors_json' => 'required|json',
            'email_styles_json' => 'nullable|json',
        ]);

        $data = $request->all();
        
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
            'colors_json' => 'required|json',
            'email_styles_json' => 'nullable|json',
        ]);

        $data = $request->except(['logo', 'favicon']);
        
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
        if ($theme->is_active) {
            return back()->with('error', 'Cannot delete the active theme.');
        }

        if ($theme->wishes()->exists()) {
            return back()->with('error', 'Cannot delete theme with associated wishes.');
        }

        // Delete associated files
        if ($theme->logo_path) {
            Storage::disk('public')->delete($theme->logo_path);
        }
        if ($theme->favicon_path) {
            Storage::disk('public')->delete($theme->favicon_path);
        }

        auth('admin')->user()->logActivity('ADMIN_THEME_DELETED', [
            'theme_id' => $theme->id,
            'theme_title' => $theme->theme_title,
            'year' => $theme->year
        ]);

        $theme->delete();

        return redirect()->route('admin.themes.index')
            ->with('success', 'Theme deleted successfully.');
    }
}