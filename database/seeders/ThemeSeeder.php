<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the 2026 theme
        Theme::create([
            'uuid' => Str::uuid(),
            'year' => 2026,
            'theme_title' => 'Three Wishes 2026',
            'theme_tagline' => 'Trust in His perfect timing',
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
}
