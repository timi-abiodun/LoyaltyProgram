<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('a user can register successfully', function () {
    // 1. Act: Hit the registration endpoint
    $response = $this->postJson('/api/v1/register', [
        'first_name' => 'John',
        'last_name'=> 'Snow',
        'username' => 'john',
        'email' => 'john@email.com',
        'password' => 'securePassword123',
        'password_confirmation' => 'securePassword123',
    ]);

    // 2. Assert: Response is correct
    $response->assertStatus(201) // 201 Created is standard for registration
             ->assertJsonStructure([
                'user' => ['id', 'username', 'email'],
                'access_token',
                'token_type'
             ]);

    // 3. Assert: Database has the user
    $this->assertDatabaseHas('users', [
        'username' => 'john',
        'email' => 'john@email.com',
    ]);

    // 4. Assert: Password is encrypted
    $user = User::where('username', 'john')->first();
    expect(Hash::check('securePassword123', $user->password))->toBeTrue();
});

test('registration fails if email is already taken', function () {
    // Arrange: Create an existing user
    User::factory()->create(['email' => 'duplicate@oau.edu.ng']);

    // Act
    $response = $this->postJson('/api/v1/register', [
        'first_name' => 'first',
        'last_name' => 'last',
        'username' => 'newuser',
        'email' => 'duplicate@oau.edu.ng',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // Assert
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
});

test('registration fails with an invalid email format', function () {
    $response = $this->postJson('/api/v1/register', [
        'first_name' => 'first',
        'last_name' => 'last',
        'username' => 'newuser',
        'email' => 'not-an-email-address',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['email']);
});

test('username must be at most 30 characters', function () {
    $response = $this->postJson('/api/v1/register', [
        'first_name' => 'first',
        'last_name' => 'last',
        'username' => str_repeat('a', 31),
        'email' => 'duplicate@oau.edu.ng',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['username']);
});

test('it fails if first_name or last_name are missing', function () {
    $this->postJson('/api/v1/register', [
        'first_name' => '',
        'last_name' => '',
    ])->assertStatus(422)
      ->assertJsonValidationErrors(['first_name', 'last_name']);
});

test('it generates a valid UUID for the user id', function () {
    $this->postJson('/api/v1/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'username' => 'testuser',
        'email' => 'test@oau.edu.ng',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::where('username', 'testuser')->first();
    
    // Assert it's a valid UUID format (e.g. 550e8400-e29b-41d4-a716-446655440000)
    expect(Str::isUuid($user->id))->toBeTrue();
});
