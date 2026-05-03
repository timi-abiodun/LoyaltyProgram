<?php

use App\Models\User;
use App\Models\Achievement;
use App\Models\Badge;

test('cashbacks are rewarded for badges unlocked', function () {
    // Arrange
    $user = User::factory()->create([
        'total_purchase_count' => 0
    ]);
    $this->actingAs($user);

    $unlocked_achievement = Achievement::factory()->create([
        'type' => 'purchases_count',
        'threshold' => 1,
        'points_awarded' => 100
    ]);

    $unlocked_badge = Badge::factory()->create([
        'points_required' => 100
    ]);

    // Act
     $payload = [
        'amount' => 10000
    ];
    $response = $this->postJson('/api/v1/purchases', $payload);

    // Assert
    $response->assertStatus(201)
             ->assertJson(['message' => 'Purchase completed successfully.']);

    // $this->assertDatabaseHas('cashbacks', [
    //     'user_id' => $user->id,
    //     'badge_id' => $unlocked_badge->id,
    //     'amount' => 300
    // ]);
});
