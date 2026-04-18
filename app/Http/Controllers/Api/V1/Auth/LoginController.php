<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private TwoFactorService $twoFactorService,
    ) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Compte désactivé. Contactez le support.'], 403);
        }

        if (! $user->email_verified_at) {
            return response()->json([
                'message' => 'Email non vérifié. Consultez votre boîte mail.',
                'requires_verification' => true,
            ], 403);
        }

        // 2FA gate: if user has 2FA enabled, return a short-lived token
        if ($this->twoFactorService->isEnabled($user)) {
            $twoFactorToken = $this->authService->createTwoFactorToken($user);

            return response()->json([
                'message'          => 'Vérification 2FA requise.',
                'requires_2fa'     => true,
                'two_factor_token' => $twoFactorToken,
            ]);
        }

        $tokens = $this->authService->createTokenPair($user);

        return response()->json([
            'message' => 'Connexion réussie.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $tokens,
            ],
        ])->withCookie(
            cookie(
                name: 'refresh_token',
                value: $tokens['refresh_token'],
                minutes: 60 * 24 * 30,
                secure: app()->isProduction(),
                httpOnly: true,
                sameSite: 'Strict'
            )
        );
    }
}
