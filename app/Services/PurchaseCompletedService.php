<?php

namespace App\Services;

use App\Models\Achievement;
use App\Events\AchievementUnlocked;

class PurchaseCompletedService
{
    public function handle($event)
    {
        $user = $event->user;

        // 1. Fetch achievements user doesnt have
        $potentialAchievements = Achievement::whereIn('type', ['amount_spent', 'purchases_count'])
            ->whereDoesntHave('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        foreach($potentialAchievements as $achievement){
            $conditionMet = false;

            if ($achievement->type === 'amount_spent'){
                $conditionMet = $user->total_amount_spent >= $achievement->threshold;
            }

            if ($achievement->type === 'purchases_count'){
                $conditionMet = $user->total_purchase_count >= $achievement->threshold;
            }

            if ($conditionMet){
                $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);

                // 2. Fire AchievementUnlocked Event
                AchievementUnlocked::dispatch($user, $achievement);
            }
        }
    }
}