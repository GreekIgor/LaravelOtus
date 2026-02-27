<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $plainToken = Str::random(60);

        /** @var \App\Models\ApiToken $token */
        $token = ApiToken::create([
            'user_id' => $user->id,
            'name' => 'api-token',
            'token' => hash('sha256', $plainToken),
            'abilities' => ['*'],
        ]);

        return response()->json([
            'token' => $plainToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization');

        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token not provided.',
            ], 400);
        }

        $plainToken = substr($authHeader, 7);

        ApiToken::where('token', hash('sha256', $plainToken))->delete();

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }
}

