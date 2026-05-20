<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'preferred_language' => $request->input('preferred_language', 'fr'),
            'is_active' => true,
        ]);
        $user->forceFill(['role' => 'tourist'])->save();

        $token = $user->createToken('maghrebpass-api-token')->plainTextToken;

        return $this->authResponse([
            'message' => 'Compte cree avec succes.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], $token, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Ce compte est desactive.',
            ], 403);
        }

        $token = $user->createToken('maghrebpass-api-token')->plainTextToken;

        return $this->authResponse([
            'message' => 'Connexion reussie.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], $token);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Deconnexion reussie.',
        ])->withoutCookie('maghrebpass_token');
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return response()->json([
            'message' => 'Profil mis a jour.',
            'user' => $request->user()->fresh(),
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->validated());

        return response()->json([
            'message' => __($status),
        ]);
    }

    private function authResponse(array $payload, string $token, int $status = 200): JsonResponse
    {
        return response()
            ->json($payload, $status)
            ->cookie(
                'maghrebpass_token',
                $token,
                60 * 24 * 7,
                '/',
                null,
                filter_var(env('COOKIE_SECURE', false), FILTER_VALIDATE_BOOLEAN),
                true,
                false,
                'Lax'
            );
    }
}
