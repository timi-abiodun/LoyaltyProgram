<?php

namespace App\Services;

use App\Models\User;
use App\Enums\AchievementType;
use Illuminate\Database\Eloquent\Collection;

class AchievementService
{
    public function __construct(
        protected iterable $progressStrategies,
    ) {}

    public function getUnlockedAchievements(User $user): Collection
    {
        // Fetch the users achievements
        $unlockedAchievements = $user->achievements()->get();
        return $unlockedAchievements;
    }

    public function getLockedAchievements(User $user, int $purchaseCount, string $amountSpent): Collection
    {
        $stats = [
            AchievementType::PURCHASES_COUNT->value => $purchaseCount,
            AchievementType::AMOUNT_SPENT->value => $amountSpent,
        ];

        $lockedAchievements = new Collection();

        foreach ($this->progressStrategies as $strategy) {
            $lockedAchievements = new Collection($lockedAchievements->merge($strategy->getLocked($user))->all());
        }

        return $lockedAchievements->sortBy(function ($achievement) use ($stats) {
            // $achievement->type is cast to AchievementType enum, so we must normalize
            // to the same scalar keys we used when building $stats.
            $typeKey = $achievement->type instanceof AchievementType
                ? $achievement->type->value
                : (string) $achievement->type;

            return $achievement->threshold - ($stats[$typeKey] ?? 0);
        })->values();
    }
}