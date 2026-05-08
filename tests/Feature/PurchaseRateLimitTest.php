<?php

use App\Models\User;

test('purchases endpoint is rate limited', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Send 3 successful requests
    for ($i = 0; $i < 3; $i++) {
        $this->postJson('/api/v1/purchases', ['amount' => 1000])->assertStatus(201);
    }

    // The 4th request should fail
    $response = $this->postJson('/api/v1/purchases', ['amount' => 1000]);
    
    $response->assertStatus(429); // Too Many Requests
});