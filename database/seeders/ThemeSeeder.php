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
            'theme_title' => 'The Year of Much More',
            'theme_tagline' => 'Trust in His perfect timing',
            'theme_verse_reference' => 'Prov 4:22',
            'theme_verse_text' => 'For they are life to those who find them, and health to all their flesh',
            'colors_json' => [
                'primary'   => '#002037', // Navy
                'secondary' => '#F8BE5D', // Gold
                'accent'    => '#23D09F', // Cyan
                'background'=> '#FFFFFF', // White
                'text'      => '#000000', // Black
                'muted'     => '#EEEEEE', // Gray
            ],
            'is_active' => true,
        ]);
    }
}
