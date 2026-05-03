<?php

namespace App\Strategies;

use App\Models\User;
use App\Models\Achievement;
use App\Enums\AchievementType;
use App\Gateways\UserStatsGatewayInterface;
use Illuminate\Database\Eloquent\Collection;

class AmountSpentProgressStrategy implements AchievementProgressStrategyInterface
{
    public function __construct(protected UserStatsGatewayInterface $gateway){}

    public function getProgress(User $user): Collection
    {
        // 1. Get the value once
        $currentAmountSpent = $this->gateway->getTotalAmountSpent($user);

        $lockedAchievements = Achievement::whereIn('type', AchievementType::AMOUNT_SPENT)
            ->where('threshold', '>', $currentAmountSpent) 
            ->whereDoesntHave('users', fn($q) => $q->where('users.id', $user->id))
            ->get()
            ->sortBy(function ($achievement) use ($currentAmountSpent) { // Pass the pre-fetched value
                return $achievement->threshold - $currentAmountSpent;
            })
            ->values();

        return $lockedAchievements;
    }
}