<?php

use App\Models\User;
use App\Enums\PurchaseStatus;

it('creates a purchase and updates user aggregates', function () {
    // arrange - create a user and authenticate
    $user = User::factory()->create([
        'total_amount_spent' => 0,
        'total_purchase_count' => 0,
    ]);    
    $this->actingAs($user);

    // act - POST /api/v1/purchases with an amount
    $payload = [
        'amount' => 10000
    ];
    $response = $this->postJson('/api/v1/purchases', $payload);

    // assert - check purchase exists in DB and user aggregates updated
    $response->assertStatus(201)
             ->assertJson(['message' => 'Purchase completed successfully.']);

    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'amount'  => 10000,
        'status'  => PurchaseStatus::COMPLETED,
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'total_amount_spent'   => 10000,
        'total_purchase_count' => 1
    ]);
});

