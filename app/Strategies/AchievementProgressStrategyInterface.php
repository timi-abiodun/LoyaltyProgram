<?php

namespace App\Strategies;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AchievementProgressStrategyInterface
{
    public function getProgress(User $user): Collection;
}