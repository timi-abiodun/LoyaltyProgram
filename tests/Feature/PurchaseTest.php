<?php

use App\Models\User;
use App\Enums\PurchaseStatus;

test('user can make a purchase', function () {
    // arrange - create a user and authenticate
    $user = User::factory()->create();    
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
});

test('it validates the purchase amount', function ($amount) {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
                     ->postJson('/api/v1/purchases', [
                         'amount' => $amount
                     ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['amount']);
})->with([
    'negative amount'     => -1,
    'zero amount'         => 0,
    'string/alphanumeric' => '100abc',
    'too many decimals'   => 10.555,
    'extremely large'     => 1000000000000,
    'null value'          => null,
    'boolean value'       => true,
]);
