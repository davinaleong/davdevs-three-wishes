<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'year',
        'theme_title',
        'theme_tagline',
        'theme_verse_reference',
        'theme_verse_text',
        'logo_path',
        'favicon_path',
        'colors_json',
        'email_styles_json',
        'is_active',
    ];

    protected $casts = [
        'colors_json' => 'array',
        'email_styles_json' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($theme) {
            $theme->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function wishes()
    {
        return $this->hasMany(Wish::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public static function getActiveTheme()
    {
        return static::active()->first();
    }

    public static function getThemeForYear($year)
    {
        return static::year($year)->first();
    }

    public function getColors($key = null)
    {
        $colors = $this->colors_json ?? [];
        return $key ? ($colors[$key] ?? null) : $colors;
    }

    public function getEmailStyles($key = null)
    {
        $styles = $this->email_styles_json ?? $this->colors_json ?? [];
        return $key ? ($styles[$key] ?? null) : $styles;
    }
}
