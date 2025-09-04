<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMembership>
 */
class UserMembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $durationDays = fake()->randomElement([30, 90, 180, 365]);
        $endDate = clone $startDate;
        $endDate->modify("+{$durationDays} days");

        return [
            'user_id' => User::factory(),
            'package_id' => MembershipPackage::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(['active', 'expired', 'cancelled']),
            'payment_method' => fake()->randomElement(['paypal', 'whatsapp']),
            'payment_status' => fake()->randomElement(['pending', 'completed', 'failed']),
            'admin_verified' => fake()->boolean(80),
            'verified_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the membership is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subDays(rand(1, 30)),
            'end_date' => now()->addDays(rand(1, 300)),
            'payment_status' => 'completed',
            'admin_verified' => true,
        ]);
    }

    /**
     * Indicate that the membership is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'start_date' => now()->subDays(rand(31, 400)),
            'end_date' => now()->subDays(rand(1, 30)),
            'payment_status' => 'completed',
            'admin_verified' => true,
        ]);
    }

    /**
     * Indicate that the membership is pending verification.
     */
    public function pendingVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'payment_status' => 'pending',
            'admin_verified' => false,
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the membership is paid via PayPal.
     */
    public function paypal(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'paypal',
            'payment_status' => 'completed',
        ]);
    }

    /**
     * Indicate that the membership is paid via WhatsApp.
     */
    public function whatsapp(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'whatsapp',
            'payment_status' => 'pending',
            'admin_verified' => false,
        ]);
    }
}
