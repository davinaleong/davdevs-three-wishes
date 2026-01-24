<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theme>
 */
class ThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->numberBetween(2020, 2030);
        
        return [
            'year' => $year,
            'theme_title' => fake()->sentence(3),
            'theme_tagline' => fake()->sentence(),
            'theme_verse_reference' => fake()->randomElement([
                'Jeremiah 29:11',
                'Proverbs 3:5-6',
                'Romans 8:28',
                'Philippians 4:13',
                'Isaiah 40:31'
            ]),
            'theme_verse_text' => fake()->paragraph(),
            'colors_json' => [
                'primary_color_hex' => fake()->hexColor(),
                'accent_color_hex' => fake()->hexColor(),
                'light_color_hex' => fake()->hexColor(),
            ],
            'is_active' => fake()->boolean(30), // 30% chance of being active
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the theme is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the theme is for the current year.
     */
    public function currentYear(): static
    {
        $currentYear = now()->year;
        
        return $this->state(fn (array $attributes) => [
            'year' => $currentYear,
            'edit_window_start' => "{$currentYear}-01-01",
            'edit_window_end' => "{$currentYear}-03-31",
        ]);
    }
}