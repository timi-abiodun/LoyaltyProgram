<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Services\CheckForBadgeUnlockService;

class CheckForBadgeUnlockListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected CheckForBadgeUnlockService $checkForBadgeUnlockService
    ) {}
    
    /**
     * Handle the event.
     */
    public function handle(AchievementUnlocked $event): void
    {
        $this->checkForBadgeUnlockService->handle($event);
    }
}
