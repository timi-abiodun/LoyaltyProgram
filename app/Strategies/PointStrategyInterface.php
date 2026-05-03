<?php

namespace App\Strategies;

use App\Models\User;

interface PointStrategyInterface
{
    public function getPoints(User $user): int;
}