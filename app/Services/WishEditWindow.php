<?php

namespace App\Services;

use App\Models\Theme;
use Carbon\Carbon;

class WishEditWindow
{
    /**
     * Check if the edit window is open for a given theme
     */
    public static function isOpen(Theme $theme, Carbon $now = null): bool
    {
        $now = $now ?: Carbon::now('Asia/Singapore');
        $currentYear = $now->year;
        
        // Special case: Current year (2026) - allow edits until Feb 26
        if ($theme->year == $currentYear) {
            $cutoffDate = Carbon::create($currentYear, 2, 26, 23, 59, 59, 'Asia/Singapore');
            return $now->lte($cutoffDate);
        }
        
        // Future years: allow edits until Jan 31 of that year
        if ($theme->year > $currentYear) {
            $cutoffDate = Carbon::create($theme->year, 1, 31, 23, 59, 59, 'Asia/Singapore');
            return $now->lte($cutoffDate);
        }
        
        // Past years: editing is always closed
        return false;
    }

    /**
     * Get the cutoff date for editing wishes for a theme
     */
    public static function getCutoffDate(Theme $theme): Carbon
    {
        $currentYear = Carbon::now('Asia/Singapore')->year;
        
        // Special case: Current year (2026) - cutoff is Feb 26
        if ($theme->year == $currentYear) {
            return Carbon::create($currentYear, 2, 26, 23, 59, 59, 'Asia/Singapore');
        }
        
        // Future years: cutoff is Jan 31 of that year
        return Carbon::create($theme->year, 1, 31, 23, 59, 59, 'Asia/Singapore');
    }

    /**
     * Get human-readable description of when editing closes
     */
    public static function getClosingDescription(Theme $theme): string
    {
        $cutoffDate = static::getCutoffDate($theme);
        $currentYear = Carbon::now('Asia/Singapore')->year;
        
        if ($theme->year == $currentYear) {
            return "Editing closes on 26 February {$theme->year}";
        }
        
        return "Editing closes on 31 January {$theme->year}";
    }
}