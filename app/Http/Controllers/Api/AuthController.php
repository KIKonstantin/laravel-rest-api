<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required' 
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if(!$user) {
            throw ValidationException::withMessages([
                'email' => ['Предоставените данни са невалидни']
            ]);
        }
        if(!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Предоставените данни са невалидни']
            ]);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'token' => $token
        ]);
    }
    public function logout(Request $request) {

    }
}
