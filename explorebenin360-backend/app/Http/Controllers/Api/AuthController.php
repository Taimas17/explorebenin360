<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole('traveler');

        event(new Registered($user));

        $token = $user->createToken('spa')->plainTextToken;
        return response()->json(['user' => $user], 201)
            ->cookie(
                'eb360_token',
                $token,
                config('session.lifetime', 120),
                '/',
                config('session.domain'),
                config('session.secure_cookie', true),
                true,
                false,
                config('session.same_site', 'lax')
            );
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        $token = $user->createToken('spa')->plainTextToken;
        return response()->json(['user' => $user])
            ->cookie(
                'eb360_token',
                $token,
                config('session.lifetime', 120),
                '/',
                config('session.domain'),
                config('session.secure_cookie', true),
                true,
                false,
                config('session.same_site', 'lax')
            );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out'])
            ->cookie('eb360_token', '', -1, '/', config('session.domain'), config('session.secure_cookie', true), true, false, config('session.same_site', 'lax'));
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()->load('roles')]);
    }
}
