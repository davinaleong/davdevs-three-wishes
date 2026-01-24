<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wish>
 */
class WishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => fake()->paragraph(3),
            'position' => fake()->numberBetween(1, 10),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the wish is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    /**
     * Indicate that the wish is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => false,
        ]);
    }

    /**
     * Set a specific prayer focus.
     */
    public function withFocus(string $focus): static
    {
        return $this->state(fn (array $attributes) => [
            'prayer_focus' => $focus,
        ]);
    }

    /**
     * Create a wish with long content.
     */
    public function longContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'wish_content' => $this->faker->paragraphs(5, true),
        ]);
    }
}