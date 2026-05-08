<?php

namespace App\Listeners;

use App\Events\PurchaseCompleted;
use App\Services\ProcessAchievementsService;

use Illuminate\Queue\InteractsWithQueue;

class ProcessAchievementsListener
{  
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected ProcessAchievementsService $processAchievementsService
    ){}

    /**
     * Handle the event.
     */
    public function handle(PurchaseCompleted $event): void
    {
        $this->processAchievementsService->handle($event);
    }
}
