<?php

namespace App\Gateways;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserStatsGatewayInterface
{
    public function getTotalPurchase(User $user): int;
    public function getTotalAmountSpent(User $user): int;
    public function getCurrentPoints(User $user): int;
    public function getStatFor(string $type, User $user);
    public function getUnlockedAchievements(User $user): Collection;
}