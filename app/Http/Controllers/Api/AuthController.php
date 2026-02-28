<?php

namespace App\Http\Controllers\Api;

use App\Domains\Core\Models\User;
use App\Http\Controllers\Controller;
use Faker\Provider\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController
{

  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => 'required|email',
      'password' => 'required|string|min:6',
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
      ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
      'user' => $user->load('patient'),
      'access_token' => $token,
      'message' => 'Login successful',
    ]);
  }

  public function logout(Request $request)
  {
    $token = $request->bearerToken();
    if (!$token) {
      return response()->json([
        'message' => 'No token provided',
      ], 401);
    }

    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken) {
      return response()->json([
        'message' => 'Invalid token',
      ], 401);
    }

    $accessToken->delete();
    return response()->json([
      'message' => 'Logout successful',
    ]);
  }

  public function user(Request $request)
  {
    $user = $request->user();
    if (!$user) {
      return response()->json([
        'message' => 'Unauthenticated',
      ], 401);
    }

    return response()->json($user->load('patient'));
  }
}
