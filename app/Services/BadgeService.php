<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BadgeService
{

    public function getCurrentBadge(int $points)
    {
        // Fetch the users current badge
        $currentBadge = Badge::where('points_required', '<=', $points)
            ->orderBy('points_required', 'desc')
            ->first();

        return $currentBadge;
    }

    public function getNextBadge(int $points)
    {
        // Fetch the users next badge        
        $nextBadge = Badge::where('points_required', '>', $points)
            ->orderBy('points_required', 'asc')
            ->first();

        return $nextBadge;
    }

    /**
     * Get all badges the user qualifies for based on points, 
     * excluding those they already own.
     */
    public function getEligibleBadges(User $user, int $points): Collection
    {
        return Badge::where('points_required', '<=', $points)
            ->whereDoesntHave('users', fn($q) => $q->where('id', $user->id))
            ->orderBy('points_required', 'asc')
            ->get();
    }

}