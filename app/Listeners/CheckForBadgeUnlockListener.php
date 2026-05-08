<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Services\CheckForBadgeUnlockService;

use Illuminate\Queue\InteractsWithQueue;

class CheckForBadgeUnlockListener
{
    use InteractsWithQueue;
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
