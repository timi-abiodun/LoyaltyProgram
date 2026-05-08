<?php

use App\Models\Achievement;
use App\Models\User;

use function Pest\Laravel\actingAs;

// (no beforeEach needed)


test('unlocking purchases_count achievement after enough purchases', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $achievement = Achievement::factory()->create([
        'type' => 'purchases_count',
        'threshold' => 2,
        'points_awarded' => 50,
    ]);

    // 1st purchase - should NOT unlock
    $response = $this->postJson('/api/v1/purchases', ['amount' => 100]);
    $response->assertStatus(201)
        ->assertJson(['message' => 'Purchase completed successfully.']);

    $this->assertDatabaseMissing('user_achievements', [
        'user_id' => $user->id,
        'achievement_id' => $achievement->id,
    ]);

    // 2nd purchase - should unlock
    $response = $this->postJson('/api/v1/purchases', ['amount' => 120]);
    $response->assertStatus(201)
        ->assertJson(['message' => 'Purchase completed successfully.']);

    $this->assertDatabaseHas('user_achievements', [
        'user_id' => $user->id,
        'achievement_id' => $achievement->id,
    ]);

    $this->assertDatabaseHas('user_achievements', [
        'user_id' => $user->id,
        'achievement_id' => $achievement->id,
    ]);
});

test('unlocking amount_spent achievement after reaching threshold amount', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $achievement = Achievement::factory()->create([
        'type' => 'amount_spent',
        'threshold' => 250,
        'points_awarded' => 80,
    ]);

    // First purchase: amount = 150 (still below threshold)
    $response = $this->postJson('/api/v1/purchases', ['amount' => 150]);
    $response->assertStatus(201)
        ->assertJson(['message' => 'Purchase completed successfully.']);

    $this->assertDatabaseMissing('user_achievements', [
        'user_id' => $user->id,
        'achievement_id' => $achievement->id,
    ]);

    // Second purchase: total = 150 + 120 = 270 (meets/exceeds threshold)
    $response = $this->postJson('/api/v1/purchases', ['amount' => 120]);
    $response->assertStatus(201)
        ->assertJson(['message' => 'Purchase completed successfully.']);

    $this->assertDatabaseHas('user_achievements', [
        'user_id' => $user->id,
        'achievement_id' => $achievement->id,
    ]);
});

