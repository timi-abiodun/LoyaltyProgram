<?php

namespace App\Services;

use App\Models\User;

class UserDashboardService
{
    public function __construct(
        protected AchievementService $achievementService,
        protected UserStatsService $userStatsService,
        protected BadgeService $badgeService,
    ){}

    public function handle(User $user): array
    {
        $purchaseCount = $this->userStatsService->getTotalPurchase($user);
        $amountSpent = $this->userStatsService->getTotalAmountSpent($user);
        $points = $this->userStatsService->getCurrentPoints($user);

        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);
        $nextAvailableAchievements = $this->achievementService->getLockedAchievements($user, $purchaseCount, $amountSpent);

        $currentBadge = $this->badgeService->getCurrentBadge($points);
        $nextBadge = $this->badgeService->getNextBadge($points);

        return [
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $nextBadge 
                ? $nextBadge->points_required - $points
                : null,
        ];
    }
}