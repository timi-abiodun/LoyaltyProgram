<?php

namespace App\Strategies;

use App\Models\User;
use App\Models\Achievement;
use App\Enums\AchievementType;
use App\Services\UserStatsService;
use Illuminate\Database\Eloquent\Collection;

class AmountSpentStrategy implements AchievementStrategyInterface
{
    public function __construct(
        protected UserStatsService $userStatsService
    ){}

    public function getLocked(User $user): Collection
    {
        $lockedAchievements = Achievement::whereIn('type', [AchievementType::AMOUNT_SPENT->value])
            ->whereDoesntHave('users', fn($q) => $q->where('users.id', $user->id))
            ->get();

        return $lockedAchievements;
    }

    public function isQualified(User $user, Achievement $achievement): bool
    {
        $currentAmount = $this->userStatsService->getTotalAmountSpent($user);

        return $currentAmount >= $achievement->threshold;
    }
}