<?php

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    // Don't fake events: we want the real listener to run.
});

test('unlocks eligible badge when user gains enough points (AchievementUnlocked)', function () {


    $user = User::factory()->create();
    $this->actingAs($user);

    // User's points come from sum(user_achievements.points_awarded)
    $achievement = Achievement::factory()->create([
        'type' => 'purchases_count',
        'threshold' => 2,
        'points_awarded' => 100,
    ]);

    $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);

    $badge = Badge::factory()->create([
        'name' => 'Test Badge',
        'points_required' => 50,
    ]);

    // Act: simulate the moment an achievement unlock happens.
    // Dispatch the event explicitly so Laravel runs the listener.
    Event::dispatch(new AchievementUnlocked($user, $achievement));


    // Assert: badge is now unlocked for the user.
    $this->assertDatabaseHas('user_badges', [
        'user_id' => $user->id,
        'badge_id' => $badge->id,
    ]);

    // Fetch the specific record to check the timestamp
    $userBadge = \DB::table('user_badges')
        ->where('user_id', $user->id)
        ->where('badge_id', $badge->id)
        ->first();

    // Assert the timestamp isn't null
    $this->assertNotNull($userBadge->unlocked_at);
});

test("does not unlock the same badge twice when checking eligibility again (AchievementUnlocked)", function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $achievement = Achievement::factory()->create([
        'type' => 'purchases_count',
        'threshold' => 2,
        'points_awarded' => 100,
    ]);
    $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);

    $badge = Badge::factory()->create([
        'name' => 'Test Badge',
        'points_required' => 50,
    ]);

    // Pre-unlock badge
    $user->badges()->attach($badge->id, ['unlocked_at' => now()]);

    // Fire listener-triggering event multiple times
    event(new AchievementUnlocked($user, $achievement));
    event(new AchievementUnlocked($user, $achievement));

    $this->assertEquals(1, \Illuminate\Support\Facades\DB::table('user_badges')
        ->where('user_id', $user->id)
        ->where('badge_id', $badge->id)
        ->count());
});

test('does not unlock badge when user does not have enough points (AchievementUnlocked)', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $achievement = Achievement::factory()->create([
        'type' => 'purchases_count',
        'threshold' => 2,
        'points_awarded' => 20,
    ]);
    $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);

    $badge = Badge::factory()->create([
        'name' => 'Too Expensive Badge',
        'points_required' => 100,
    ]);

    event(new AchievementUnlocked($user, $achievement));

    $this->assertDatabaseMissing('user_badges', [
        'user_id' => $user->id,
        'badge_id' => $badge->id,
    ]);
});

test('unlocks multiple eligible badges at once if user exceeds multiple thresholds', function () {
    $user = User::factory()->create();
    
    // Total 150 points
    $achievement = Achievement::factory()->create(['points_awarded' => 150]);
    $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);

    // Two different badges the user now qualifies for
    $badge1 = Badge::factory()->create(['points_required' => 50]);
    $badge2 = Badge::factory()->create(['points_required' => 100]);
    $badge3 = Badge::factory()->create(['points_required' => 200]); // Too expensive

    event(new AchievementUnlocked($user, $achievement));

    $this->assertDatabaseHas('user_badges', ['badge_id' => $badge1->id]);
    $this->assertDatabaseHas('user_badges', ['badge_id' => $badge2->id]);
    $this->assertDatabaseMissing('user_badges', ['badge_id' => $badge3->id]);
});