<?php

namespace App\Strategies;

use App\Models\User;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Collection;

interface AchievementStrategyInterface
{
    public function getLocked(User $user): Collection;
    public function isQualified(User $user, Achievement $achievement): bool;
}