<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Services\AwardBadgeCashbackService;

class AwardBadgeCashbackListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected AwardBadgeCashbackService $awardBadgeCashbackService
    ){}

    /**
     * Handle the event.
     */
    public function handle(BadgeUnlocked $event): void
    {
        $this->awardBadgeCashbackService->handle($event);
    }
}
