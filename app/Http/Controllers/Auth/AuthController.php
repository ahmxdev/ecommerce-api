<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'user registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }
    public function login(Request $request) {}
    public function logout(Request $request) {}
    public function me(Request $request) {}
}
