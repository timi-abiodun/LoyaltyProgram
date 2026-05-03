<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Badge;
use App\Models\Cashback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cashback>
 */
class CashbackFactory extends Factory
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
            'badge_id' => Badge::factory(),
            'amount' => 300.00,
            'created_at' => now(),
        ];
    }
}
