<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

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
            'phone' => fake()->phoneNumber(),
            'whatsapp_verified' => fake()->boolean(80),
            'role' => 'student',
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
            'avatar' => null,
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'country' => fake()->country(),
            'timezone' => fake()->timezone(),
            'preferences' => [
                'notifications' => [
                    'email' => fake()->boolean(),
                    'sms' => fake()->boolean(),
                    'whatsapp' => fake()->boolean(),
                ],
                'language' => fake()->randomElement(['ar', 'en']),
                'theme' => fake()->randomElement(['light', 'dark']),
            ],
            'email_verified_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'password' => Hash::make('password'), // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a student.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is a teacher.
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'teacher',
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the user is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    /**
     * Indicate that the user has WhatsApp verified.
     */
    public function whatsappVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'whatsapp_verified' => true,
        ]);
    }

    /**
     * Indicate that the user has an avatar.
     */
    public function withAvatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'avatar' => 'users/' . fake()->numberBetween(1, 100) . '/avatars/avatar.jpg',
        ]);
    }
}
