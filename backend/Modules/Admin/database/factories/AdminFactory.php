<?php

namespace Modules\Admin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Admin\Models\Admin;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Admin\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('password'), // password
            'avatar' => null,
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
            'last_login_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'last_login_ip' => fake()->optional()->ipv4(),
            'last_login_user_agent' => fake()->optional()->userAgent(),
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'email_verified_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'password_changed_at' => fake()->optional()->dateTimeBetween('-6 months', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the admin is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the admin is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the admin is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    /**
     * Indicate that the admin has email verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the admin has two-factor authentication enabled.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_enabled' => true,
            'two_factor_secret' => 'secret',
            'two_factor_recovery_codes' => ['code1', 'code2', 'code3'],
        ]);
    }

    /**
     * Indicate that the admin has an avatar.
     */
    public function withAvatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'avatar' => 'admins/' . fake()->numberBetween(1, 100) . '/avatars/avatar.jpg',
        ]);
    }
}
