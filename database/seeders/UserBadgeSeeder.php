<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserBadgeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = User::all();
        $badges = Badge::query()->orderBy('points_required')->get();

        if ($users->isEmpty() || $badges->isEmpty()) {
            return;
        }

        // Assign badges by tier based on the user's current points (computed from unlocked achievements).
        // This keeps dashboard values consistent.
        foreach ($users as $idx => $user) {
            $points = (int) $user->achievements()->sum('points_awarded');

            // Current badge is <= points; next badge is > points.
            $current = $badges
                ->where('points_required', '<=', $points)
                ->sortByDesc('points_required')
                ->first();

            $next = $badges
                ->where('points_required', '>', $points)
                ->sortBy('points_required')
                ->first();

            $now = Carbon::now();

            if ($current) {
                $user->badges()->syncWithoutDetaching([
                    $current->id => ['unlocked_at' => $now->copy()->subDays(max(0, 20 - $idx))],
                ]);
            }

            if ($next) {
                // Also attach one additional badge above current to make the "next badge" visible.
                $user->badges()->syncWithoutDetaching([
                    $next->id => ['unlocked_at' => $now->copy()->subDays(max(0, 10 - $idx))],
                ]);
            }

            // Optional: if user has points high enough to qualify for multiple badges, attach all up to current.
            if ($current) {
                $qualifiedIds = $badges
                    ->where('points_required', '<=', $points)
                    ->pluck('id')
                    ->all();

                $pivot = [];
                foreach ($qualifiedIds as $bIdx => $badgeId) {
                    $pivot[$badgeId] = [
                        'unlocked_at' => $now->copy()->subDays(max(0, 20 - $bIdx - $idx)),
                    ];
                }

                $user->badges()->syncWithoutDetaching($pivot);
            }
        }
    }
}

