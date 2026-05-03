<?php

namespace App\Strategies;

use App\Models\User;
use App\Models\Achievement;
use App\Enums\AchievementType;
use App\Gateways\UserStatsGatewayInterface;
use Illuminate\Database\Eloquent\Collection;

class PurchaseCountProgressStrategy implements AchievementProgressStrategyInterface
{
    public function __construct(protected UserStatsGatewayInterface $gateway){}

    public function getProgress(User $user): Collection
    {
        // 1. Get the value once
        $currentPurchaseCount = $this->gateway->getTotalPurchase($user);

        $lockedAchievements = Achievement::whereIn('type', AchievementType::PURCHASES_COUNT)
            ->where('threshold', '>', $currentPurchaseCount) 
            ->whereDoesntHave('users', fn($q) => $q->where('users.id', $user->id))
            ->get()
            ->sortBy(function ($achievement) use ($currentPurchaseCount) { // Pass the pre-fetched value
                return $achievement->threshold - $currentPurchaseCount;
            })
            ->values();

        return $lockedAchievements;
    }
}