<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class FacebookCallbackController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private TwoFactorService $twoFactorService,
    ) {}

    /**
     * Handle the Facebook OAuth callback.
     * Exchange the authorization code for user info, create/link account.
     *
     * GET /api/v1/auth/facebook/callback
     */
    public function __invoke(): JsonResponse
    {
        try {
            $socialUser = Socialite::driver('facebook')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur d\'authentification Facebook.',
            ], 401);
        }

        $result = $this->authService->handleSocialLogin('facebook', $socialUser);

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
            'message' => 'Connexion Facebook réussie.',
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
