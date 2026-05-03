<?php

namespace App\Strategies;

use App\Models\User;

class AchievementPointStrategy implements PointStrategyInterface
{
    public function getPoints(User $user): int
    {
        return (int) $user->achievements()->sum('points_awarded');
    }
}