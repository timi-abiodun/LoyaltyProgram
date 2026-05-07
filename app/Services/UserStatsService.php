<?php

namespace App\Services;

use App\Models\User;


class UserStatsService
{
    public function getTotalPurchase(User $user): int 
    {
        $purchaseCount = $user->purchases()->count();
        return (int) $purchaseCount;
    }

    public function getTotalAmountSpent(User $user): int
    {
        $amountSpent = $user->purchases()->sum("amount");
        return (int) $amountSpent;
    }

    public function getCurrentPoints(User $user): int
    {
        return (int) $user->achievements()->sum('points_awarded');
    }
}

