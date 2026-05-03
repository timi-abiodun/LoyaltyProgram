<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
// use Illuminate\Database\Eloquent\Collection;

use App\Gateways\UserStatsGatewayInterface;

class UserDashboardService
{
    public function __construct(
        protected UserStatsGatewayInterface $gateway,
        protected array $progressStrategies
        ){}

    public function fetchUnlockedAchievements(User $user){
        $unlockedAchievements = $this->gateway->getUnlockedAchievements($user);
        return $unlockedAchievements;
    }

    public function fetchNextAvailableAchievements(User $user){
       $collection = collect();

        foreach ($this->progressStrategies as $strategy) {
            $collection = $collection->merge($strategy->getProgress($user));
        }

        // Final sort: Put the "closest" achievement at the top, regardless of type
        return $collection->sortBy(function ($achievement) use ($user) {
            return $achievement->threshold - $this->gateway->getStatFor($achievement->type, $user);
        })->values();
    }

    public function handle(User $user): array {
        $currentBadge = Badge::where('points_required', '<=', $user->current_points)
            ->orderBy('points_required', 'desc')->first();
        
        $nextBadge = Badge::where('points_required', '>', $user->current_points)
            ->orderBy('points_required', 'asc')->first();

        return [
            'unlocked_achievements' => $this->fetchUnlockedAchievements($user),
            'next_available_achievements' => $this->fetchNextAvailableAchievements($user),
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $nextBadge 
                ? $nextBadge->points_required - $user->current_points 
                : null,
        ];
    }
}