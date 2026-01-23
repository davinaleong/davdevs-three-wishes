<?php

namespace App\Services;

use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    /**
     * Get the active theme (cached for performance)
     */
    public static function getActiveTheme(): ?Theme
    {
        return Cache::remember('theme.active', 3600, function () {
            return Theme::getActiveTheme();
        });
    }

    /**
     * Get theme for a specific year
     */
    public static function getThemeForYear(int $year): ?Theme
    {
        return Cache::remember("theme.year.{$year}", 3600, function () use ($year) {
            return Theme::getThemeForYear($year);
        });
    }

    /**
     * Get current year theme or create a default one
     */
    public static function getCurrentYearTheme(): Theme
    {
        $currentYear = Carbon::now()->year;
        $theme = static::getThemeForYear($currentYear);
        
        if (!$theme) {
            $theme = static::createDefaultTheme($currentYear);
        }
        
        return $theme;
    }

    /**
     * Create a default theme for the given year
     */
    public static function createDefaultTheme(int $year): Theme
    {
        return Theme::create([
            'year' => $year,
            'theme_title' => "Three Wishes {$year}",
            'theme_tagline' => "Trust in His perfect timing",
            'theme_verse_reference' => 'Jer 29:11',
            'theme_verse_text' => 'For I know the thoughts that I think toward you, says the Lord, thoughts of peace and not of evil, to give you a future and a hope.',
            'colors_json' => [
                'primary' => '#6366f1',
                'secondary' => '#8b5cf6',
                'accent' => '#06b6d4',
                'background' => '#f8fafc',
                'text' => '#1e293b',
                'muted' => '#64748b',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Apply theme colors as CSS variables
     */
    public static function getCssVariables(Theme $theme): string
    {
        $colors = $theme->getColors();
        $css = ':root {';
        
        foreach ($colors as $key => $value) {
            $css .= "--color-{$key}: {$value};";
        }
        
        $css .= '}';
        return $css;
    }

    /**
     * Clear theme cache
     */
    public static function clearCache(): void
    {
        Cache::forget('theme.active');
        // Clear year-specific caches - this is a simple approach
        for ($year = 2020; $year <= 2050; $year++) {
            Cache::forget("theme.year.{$year}");
        }
    }
}