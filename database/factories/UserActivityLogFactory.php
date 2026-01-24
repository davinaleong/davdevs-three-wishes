<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserActivityLog>
 */
class UserActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activity = $this->faker->randomElement([
            'login',
            'logout',
            'wish_created',
            'wish_updated',
            'wish_deleted',
            'profile_updated',
            'email_verified',
            'password_changed',
            'two_factor_enabled',
            'two_factor_disabled'
        ]);

        return [
            'user_id' => User::factory(),
            'activity_type' => $activity,
            'activity_data' => $this->generateActivityData($activity),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Generate activity-specific data.
     */
    private function generateActivityData(string $activity): array
    {
        switch ($activity) {
            case 'login':
                return ['method' => 'web', 'remember' => $this->faker->boolean()];
            
            case 'logout':
                return ['session_duration' => $this->faker->numberBetween(300, 7200)];
            
            case 'wish_created':
                return [
                    'wish_id' => $this->faker->numberBetween(1, 1000),
                    'privacy' => $this->faker->randomElement(['private', 'public']),
                    'content_length' => $this->faker->numberBetween(50, 1000)
                ];
            
            case 'wish_updated':
                return [
                    'wish_id' => $this->faker->numberBetween(1, 1000),
                    'fields_changed' => $this->faker->randomElements(['content', 'tags', 'privacy', 'prayer_focus'], 2)
                ];
            
            case 'wish_deleted':
                return ['wish_id' => $this->faker->numberBetween(1, 1000)];
            
            case 'profile_updated':
                return [
                    'fields_changed' => $this->faker->randomElements(['name', 'email', 'bio'], 2)
                ];
            
            case 'email_verified':
                return ['email' => $this->faker->email()];
            
            case 'password_changed':
                return ['method' => 'profile_settings'];
            
            case 'two_factor_enabled':
                return ['method' => 'app'];
            
            case 'two_factor_disabled':
                return ['reason' => 'user_request'];
            
            default:
                return [];
        }
    }

    /**
     * Create a login activity log.
     */
    public function login(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => 'login',
            'activity_data' => ['method' => 'web', 'remember' => $this->faker->boolean()],
        ]);
    }

    /**
     * Create a wish-related activity log.
     */
    public function wishActivity(): static
    {
        $activity = $this->faker->randomElement(['wish_created', 'wish_updated', 'wish_deleted']);
        
        return $this->state(fn (array $attributes) => [
            'activity_type' => $activity,
            'activity_data' => $this->generateActivityData($activity),
        ]);
    }

    /**
     * Create activity from specific IP.
     */
    public function fromIp(string $ip): static
    {
        return $this->state(fn (array $attributes) => [
            'ip_address' => $ip,
        ]);
    }
}