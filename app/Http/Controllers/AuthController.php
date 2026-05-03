<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
        // 1. Strict Validation
        $fields = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed' // Looks for password_confirmation
        ]);

        // 2. Create User
        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'username' => $fields['username'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']), //HASH passwords
        ]);

        // 3. Generate Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request){
        // 1. Validate Input
        $request->validate([
            'username'=> 'required',
            'password' => 'required'
        ]);
        
        // 2. Find user
        $user = User::where('username', $request->username)->first();

        // Check If user doesn't exist or password fails
        if (! $user || ! Hash::check($request->password, $user->password)){
           return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        //4. Create and return token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
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

