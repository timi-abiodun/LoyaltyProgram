<?php

namespace App\Gateways;

use App\Models\User;
use App\Enums\AchievementType;
use Illuminate\Database\Eloquent\Collection;


class EloquentUserStatsGateway implements UserStatsGatewayInterface
{
    protected array $strategies = [];

    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function getTotalPurchase(User $user): int 
    {
        $purchaseCount = $user->purchases()->count();
        return (int) $purchaseCount;
    }

    public function getTotalAmountSpent(User $user): int
    {
        $amountSpent = $user->purchases()->sum("amount");
        return $amountSpent;
    }

    public function getCurrentPoints(User $user): int
    {
        $total = 0;

        foreach ($this->strategies as $strategy) {
            $total += $strategy->getPoints($user);
        }

        return $total;
    }

    public function getStatFor(string $type, User $user)
    {
        return match($type) {
            AchievementType::PURCHASES_COUNT->value => $this->getTotalPurchase($user),
            AchievementType::AMOUNT_SPENT->value => $this->getTotalAmountSpent($user),
            default => 0,
        };
    }

    public function getUnlockedAchievements(User $user): Collection
    {
        $unlockedAchievements = $user->achievements()->get();

        return $unlockedAchievements;
    }
}

