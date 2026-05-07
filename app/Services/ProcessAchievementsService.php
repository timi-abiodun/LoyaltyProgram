<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use Illuminate\Support\Facades\DB;

class ProcessAchievementsService
{    
    public function __construct(
        protected iterable $strategies,
    ) {}

    public function handle($event)
    {
        $user = $event->user;

        DB::transaction(function () use ($user) {
            foreach ($this->strategies as $strategy) {
                foreach ($strategy->getLocked($user) as $achievement) {
                    if ($strategy->isQualified($user, $achievement)) {
                        $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);

                        AchievementUnlocked::dispatch($user, $achievement);
                    }
                }
            }
        });
    }

}