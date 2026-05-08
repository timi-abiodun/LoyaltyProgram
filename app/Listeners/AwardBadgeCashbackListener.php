<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Services\AwardBadgeCashbackService;

use Illuminate\Queue\InteractsWithQueue;

class AwardBadgeCashbackListener
{
    use InteractsWithQueue;
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
