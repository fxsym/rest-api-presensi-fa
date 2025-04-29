<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'user_input' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        if (Str::contains($request->user_input, '@')) {
            $user = User::where('email', $request->user_input)->first();
        } else {
            $user = User::where('username', $request->user_input)->first();
        }


        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Login Succesfully',
            'token' => $token,
            'user' => new UserResource(User::with(['honor'])->findOrFail($user->id))
        ], 200);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();  //Revoke all tokens...
        return response()->json([
            'message' => 'Logout Succes'
        ]);
    }
}
