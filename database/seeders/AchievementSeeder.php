<?php

namespace Database\Seeders;

use App\Enums\AchievementType;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            // Purchase count milestones
            [
                'name'           => 'First Order',
                'type'           => AchievementType::PURCHASES_COUNT,
                'threshold'      => 1,
                'points_awarded' => 10,
            ],
            [
                'name'           => 'Regular Shopper',
                'type'           => AchievementType::PURCHASES_COUNT,
                'threshold'      => 5,
                'points_awarded' => 30,
            ],
            [
                'name'           => 'Frequent Buyer',
                'type'           => AchievementType::PURCHASES_COUNT,
                'threshold'      => 10,
                'points_awarded' => 75,
            ],
            [
                'name'           => 'Power Shopper',
                'type'           => AchievementType::PURCHASES_COUNT,
                'threshold'      => 25,
                'points_awarded' => 150,
            ],
            [
                'name'           => 'Elite Customer',
                'type'           => AchievementType::PURCHASES_COUNT,
                'threshold'      => 50,
                'points_awarded' => 300,
            ],

            // Amount spent milestones (in Naira)
            [
                'name'           => 'First Spend',
                'type'           => AchievementType::AMOUNT_SPENT,
                'threshold'      => 1000,
                'points_awarded' => 10,
            ],
            [
                'name'           => 'Bronze Spender',
                'type'           => AchievementType::AMOUNT_SPENT,
                'threshold'      => 5000,
                'points_awarded' => 40,
            ],
            [
                'name'           => 'Silver Spender',
                'type'           => AchievementType::AMOUNT_SPENT,
                'threshold'      => 20000,
                'points_awarded' => 100,
            ],
            [
                'name'           => 'Gold Spender',
                'type'           => AchievementType::AMOUNT_SPENT,
                'threshold'      => 50000,
                'points_awarded' => 250,
            ],
            [
                'name'           => 'Whale',
                'type'           => AchievementType::AMOUNT_SPENT,
                'threshold'      => 100000,
                'points_awarded' => 600,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(
                ['name' => $achievement['name']],
                $achievement
            );
        }
    }
}