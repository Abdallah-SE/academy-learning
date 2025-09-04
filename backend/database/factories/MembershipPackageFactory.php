<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipPackage>
 */
class MembershipPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 9.99, 99.99),
            'duration_days' => fake()->randomElement([30, 90, 180, 365]),
            'features' => [
                'quran_access' => fake()->boolean(),
                'arabic_lessons' => fake()->boolean(),
                'islamic_studies' => fake()->boolean(),
                'homework_support' => fake()->boolean(),
                'meeting_access' => fake()->boolean(),
                'progress_tracking' => fake()->boolean(),
            ],
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the package is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create a basic package.
     */
    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic Package',
            'price' => 19.99,
            'duration_days' => 30,
            'features' => [
                'quran_access' => true,
                'arabic_lessons' => false,
                'islamic_studies' => false,
                'homework_support' => false,
                'meeting_access' => false,
                'progress_tracking' => true,
            ],
        ]);
    }

    /**
     * Create a standard package.
     */
    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Standard Package',
            'price' => 39.99,
            'duration_days' => 90,
            'features' => [
                'quran_access' => true,
                'arabic_lessons' => true,
                'islamic_studies' => false,
                'homework_support' => true,
                'meeting_access' => false,
                'progress_tracking' => true,
            ],
        ]);
    }

    /**
     * Create a premium package.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium Package',
            'price' => 79.99,
            'duration_days' => 180,
            'features' => [
                'quran_access' => true,
                'arabic_lessons' => true,
                'islamic_studies' => true,
                'homework_support' => true,
                'meeting_access' => true,
                'progress_tracking' => true,
            ],
        ]);
    }
}
