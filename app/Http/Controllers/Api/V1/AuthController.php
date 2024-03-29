<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $params = $request->validated();

        if(Auth::attempt($params)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'access_token' => $token,
            ];
        }

        return 'Invalid credentials.';
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return 'Logged out successfully.';
    }

    public function me()
    {
        $user = Auth::user();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'company' => $user->company?->only('id', 'name')
        ];
    }
}
