<?php

namespace Database\Factories;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(), // This magically creates a user for the purchase
            'amount' => fake()->randomNumber(), // Realistically, people don't spend $5000 every day
            // 'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
