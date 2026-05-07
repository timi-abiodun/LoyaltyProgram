<?php

namespace App\Services;

use App\Events\BadgeUnlocked;
use Illuminate\Support\Facades\DB;

class CheckForBadgeUnlockService
{
    public function __construct(
        protected BadgeService $badgeService,
        protected UserStatsService $userStatsService,
    ) {}

    public function handle($event)
    {
        $user = $event->user;
        $points = $this->userStatsService->getCurrentPoints($user);

        // Get ALL badges they earned but don't have
        $newBadges = $this->badgeService->getEligibleBadges($user, $points);

        foreach ($newBadges as $badge) {
            DB::transaction(function () use ($user, $badge) {
                $user->badges()->syncWithoutDetaching([
                    $badge->id => ['unlocked_at' => now()]
                ]);
                DB::afterCommit(fn() => BadgeUnlocked::dispatch($user, $badge));
            });
        }
    }
}