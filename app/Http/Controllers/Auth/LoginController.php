<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => __('auth.failed'),
            ], 422);
        }

        $tokenName = config('auth.personal_access_tokens.login.name', 'auth_token');
        $rememberMe = (bool) ($validated['remember_me'] ?? false);

        $defaultHours = (int) config('auth.personal_access_tokens.login.expires_in.default', 2);
        $rememberDays = (int) config('auth.personal_access_tokens.login.expires_in.remember', 30);

        $expiresAt = $rememberMe
            ? now()->addDays($rememberDays)
            : now()->addHours($defaultHours);

        $user->tokens()->where('name', $tokenName)->delete();

        $token = $user->createToken($tokenName, ['*'], $expiresAt);

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => new UserResource($user),
        ]);
    }
}
