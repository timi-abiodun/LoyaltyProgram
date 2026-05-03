<?php

namespace App\Strategies;

use App\Models\User;

class PurchasePointsStrategy implements PointStrategyInterface
{
    public function getPoints(User $user): int
    {
        $pointsAwarded = 5;
        // Example: 5 points for every purchase made
        return $user->purchases()->count() * $pointsAwarded;
    }
}