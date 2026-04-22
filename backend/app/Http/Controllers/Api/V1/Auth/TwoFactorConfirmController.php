<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\TwoFactorCodeRequest;
use App\Services\TwoFactorService;
use Illuminate\Http\JsonResponse;

class TwoFactorConfirmController extends Controller
{
    public function __construct(private TwoFactorService $twoFactorService) {}

    /**
     * Confirm 2FA setup by verifying a TOTP code.
     * Must be called after /2fa/enable.
     *
     * POST /api/v1/auth/2fa/confirm
     */
    public function __invoke(TwoFactorCodeRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($this->twoFactorService->isEnabled($user)) {
            return response()->json([
                'message' => 'L\'authentification 2FA est déjà confirmée.',
            ], 422);
        }

        if (! $user->two_factor_secret) {
            return response()->json([
                'message' => 'Activez d\'abord la 2FA via /2fa/enable.',
            ], 422);
        }

        $confirmed = $this->twoFactorService->confirm($user, $request->validated('code'));

        if (! $confirmed) {
            return response()->json([
                'message' => 'Code 2FA invalide.',
            ], 422);
        }

        return response()->json([
            'message' => 'Authentification 2FA activée avec succès.',
        ]);
    }
}
