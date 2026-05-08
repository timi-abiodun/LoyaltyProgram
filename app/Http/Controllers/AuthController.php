<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        // 1. Fetch validated input
        $data = $request->validated();

        // 2. Create User
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), //HASH passwords
        ]);

        // 3. Generate Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request){
        // 1. Fetch Validated Input
        $data = $request->validated();
        
        // 2. Find user
        $user = User::where('username', $data['username'])->first();

        // Check If user doesn't exist or password fails
        if (! $user || ! Hash::check($data['password'], $user->password)){
           return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        //4. Revoke previous tokens (prevents multiple active sessions)
        $user->tokens()->delete();

        //5. Create and return token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout(Request $request){
            // Delete the token that was used to authenticate the current request
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
    }
}

