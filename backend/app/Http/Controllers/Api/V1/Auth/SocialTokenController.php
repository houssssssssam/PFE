<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\SocialTokenRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class SocialTokenController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private TwoFactorService $twoFactorService,
    ) {}

    /**
     * Exchange a native mobile OAuth token for Sanctum tokens.
     * Used by Flutter clients that handle OAuth natively.
     *
     * POST /api/v1/auth/social/token
     */
    public function __invoke(SocialTokenRequest $request): JsonResponse
    {
        $provider = $request->validated('provider');
        $token    = $request->validated('token');

        try {
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($token);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token social invalide.',
            ], 401);
        }

        $result = $this->authService->handleSocialLogin($provider, $socialUser);

        // Check if 2FA is enabled
        if ($this->twoFactorService->isEnabled($result['user'])) {
            $twoFactorToken = $this->authService->createTwoFactorToken($result['user']);
            $result['user']->tokens()->where('name', '!=', '2fa_token')->delete();

            return response()->json([
                'message'          => 'Vérification 2FA requise.',
                'requires_2fa'     => true,
                'two_factor_token' => $twoFactorToken,
            ]);
        }

        return response()->json([
            'message' => 'Connexion réussie.',
            'data'    => [
                'user'  => new UserResource($result['user']),
                'token' => $result['tokens'],
            ],
        ])->withCookie(
            cookie(
                name: 'refresh_token',
                value: $result['tokens']['refresh_token'],
                minutes: 60 * 24 * 30,
                secure: app()->isProduction(),
                httpOnly: true,
                sameSite: 'Strict'
            )
        );
    }
}
