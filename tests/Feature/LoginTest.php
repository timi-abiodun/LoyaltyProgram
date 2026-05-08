<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

beforeEach(function () {
    RateLimiter::for('auth', function () {
        return Limit::none(); // Removes the limit for the test environment
    });
});

test('user can login with correct credentials', function () {
    // 1. Arrange: Create a user
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    // 2. Act: Hit the login endpoint
    $response = $this->postJson('/api/v1/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    // 3. Assert: Check status and token
    $response->assertStatus(200)
             ->assertJsonStructure(['access_token']);
    
    // Grab the token from the response
    $token = $response->json('access_token');

    // ACTUALLY authenticate the next request with that token
    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->getJson('/api/v1/user') // Or any protected route
         ->assertStatus(200)
         ->assertJsonPath('username', $user->username);
});

test('user cannot login with incorrect password', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/login', [
        'username' => $user->username,
        'password' => 'wrong_password',
    ]);

    $response->assertStatus(401);
    $this->assertGuest();
});

test('login fails if username does not exist', function () {
    $response = $this->postJson('/api/v1/login', [
        'username' => 'non_existent_user',
        'password' => 'any_password',
    ]);

    $response->assertStatus(401)
             ->assertJson(['message' => 'The provided credentials are incorrect.']);
});

test('login requires both username and password', function () {
    $response = $this->postJson('/api/v1/login', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['username', 'password']);
});
