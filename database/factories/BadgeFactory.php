<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            // Using a word/jobTitle is fine, but 'unique' is safer here than on numbers
            'name' => $this->faker->unique()->word() . ' Badge',
            
            // Remove 'unique'. Give it a low default so tests "accidentally" pass 
            // unless you explicitly override it.
            'points_required' => $this->faker->numberBetween(10, 500), 
        ];
    }
}

// namespace Database\Factories;

// use App\Models\Purchase;
// use Illuminate\Database\Eloquent\Factories\Factory;

// /**
//  * @extends Factory<Purchase>
//  */
// class BadgeFactory extends Factory
// {
//     /**
//      * Define the model's default state.
//      *
//      * @return array<string, mixed     */
//     public function definition(): array
//     {
//         return [
//             'name' => $this->faker->word(),
//             'points_required' => $this->faker->numberBetween(0, 5000),
//         ];
//     }
// }
