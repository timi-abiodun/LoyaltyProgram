<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAcheivementSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = User::all();
        $achievements = Achievement::query()->orderBy('threshold')->get();

        if ($users->isEmpty() || $achievements->isEmpty()) {
            return;
        }

        // Ensure each user has a different (but deterministic) unlocked set.
        // We unlock:
        // - some purchase-count achievements
        // - some amount-spent achievements
        // Then UserBadgeSeeder can compute points from unlocked achievements.
        $purchaseCount = $achievements->where('type', 'purchases_count')->sortBy('threshold')->values();
        $amountSpent = $achievements->where('type', 'amount_spent')->sortBy('threshold')->values();

        $now = Carbon::now();

        foreach ($users as $idx => $user) {
            // pick how many unlocked in each category (bounded)
            $unlockCountPurchases = min($purchaseCount->count(), ($idx % max(1, $purchaseCount->count())) + 1);
            $unlockCountSpent = min($amountSpent->count(), ((($idx + 1) * 2) % max(1, $amountSpent->count())) + 1);

            $pivot = [];

            // Attach unlocked achievements with staggered timestamps.
            for ($i = 0; $i < $unlockCountPurchases; $i++) {
                $a = $purchaseCount[$i];
                $pivot[$a->id] = ['unlocked_at' => $now->copy()->subDays(max(0, 30 - $idx - $i))];
            }

            for ($i = 0; $i < $unlockCountSpent; $i++) {
                $a = $amountSpent[$i];
                $pivot[$a->id] = ['unlocked_at' => $now->copy()->subDays(max(0, 25 - $idx - $i))];
            }

            $user->achievements()->syncWithoutDetaching($pivot);
        }
    }
}

