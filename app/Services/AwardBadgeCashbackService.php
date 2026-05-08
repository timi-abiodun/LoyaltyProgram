<?php

namespace App\Services;

use App\Models\Cashback;
use Illuminate\Support\Facades\Log;

class AwardBadgeCashbackService
{
    public function handle($event)
    {
        $user = $event->user;
        $badge = $event->badge;

        $reward = config("loyalty.cashback_amount", 0);

        // If reward is zero or null, don't waste DB rows
        if ($reward <= 0) {
            return;
        }

        $cashback = Cashback::firstOrCreate(
            ['user_id' => $user->id, 'badge_id'=> $badge->id],
            ['amount' => $reward, 'created_at' => now()]
        );

        if ($cashback->wasRecentlyCreated) {
            // Mocking the payment provider
            Log::info("CASHBACK PAID: 300 Naira sent to User {$user->username} for Badge: {$badge->name}");
        }
    }
    
}