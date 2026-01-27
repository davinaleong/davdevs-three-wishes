<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminActivityLog>
 */
class AdminActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => Admin::factory(),
            'action' => fake()->randomElement([
                'ADMIN_LOGIN',
                'ADMIN_LOGOUT',
                'ADMIN_THEME_CREATED',
                'ADMIN_THEME_UPDATED',
                'ADMIN_THEME_ACTIVATED',
                'ADMIN_USERS_VIEWED',
                'ADMIN_USER_VIEWED',
                'ADMIN_EMAIL_SENT',
            ]),
            'meta' => [
                'ip' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
            ],
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
