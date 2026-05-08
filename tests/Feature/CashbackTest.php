<?php

use App\Models\Badge;
use App\Models\Cashback;
use App\Models\User;
use App\Events\BadgeUnlocked;
use Illuminate\Support\Facades\Event;


test('cashbacks are rewarded for badges unlocked (BadgeUnlocked)', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $badge = Badge::factory()->create(['points_required' => 100]);

    // Precondition: no cashback exists
    $this->assertDatabaseMissing('cashbacks', [
        'user_id' => $user->id,
        'badge_id' => $badge->id,
    ]);

    // Act: badge unlock triggers cashback listener.
    Event::dispatch(new BadgeUnlocked($user, $badge));

    // Assert
    $this->assertDatabaseHas('cashbacks', [
        'user_id' => $user->id,
        'badge_id' => $badge->id,
        'amount' => config('loyalty.cashback_amount'),
    ]);
});

test('cashbacks are rewarded only once per badge unlocked (BadgeUnlocked)', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $badge = Badge::factory()->create(['points_required' => 100]);

    Event::dispatch(new BadgeUnlocked($user, $badge));
    Event::dispatch(new BadgeUnlocked($user, $badge));

    $this->assertEquals(1, Cashback::query()
        ->where('user_id', $user->id)
        ->where('badge_id', $badge->id)
        ->count());
});

test('it does not reward cashback if config amount is zero or missing', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    // Force the config to 0 or null
    config(['loyalty.cashback_amount' => 0]);

    // Act
    Event::dispatch(new BadgeUnlocked($user, $badge));

    // Assert: No record should be created
    $this->assertDatabaseMissing('cashbacks', [
        'user_id' => $user->id,
        'badge_id' => $badge->id,
    ]);
});

test('it rewards multiple cashbacks when multiple badges are unlocked simultaneously', function () {
    $user = User::factory()->create();
    
    // Create two different badges
    $bronzeBadge = Badge::factory()->create(['name' => 'Bronze', 'points_required' => 100]);
    $silverBadge = Badge::factory()->create(['name' => 'Silver', 'points_required' => 200]);

    // Act: Dispatch the event for both badges
    Event::dispatch(new BadgeUnlocked($user, $bronzeBadge));
    Event::dispatch(new BadgeUnlocked($user, $silverBadge));

    // Assert: Two separate cashback records exist
    $this->assertDatabaseCount('cashbacks', 2);

    $this->assertDatabaseHas('cashbacks', [
        'user_id' => $user->id,
        'badge_id' => $bronzeBadge->id,
        'amount' => config('loyalty.cashback_amount'),
    ]);

    $this->assertDatabaseHas('cashbacks', [
        'user_id' => $user->id,
        'badge_id' => $silverBadge->id,
        'amount' => config('loyalty.cashback_amount'),
    ]);

    $totalCashback = Cashback::where('user_id', $user->id)->sum('amount');
    $expectedTotal = config('loyalty.cashback_amount') * 2;

    $this->assertEquals($expectedTotal, $totalCashback);
});
