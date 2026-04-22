<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\TwoFactorCodeRequest;
use App\Services\TwoFactorService;
use Illuminate\Http\JsonResponse;

class TwoFactorDisableController extends Controller
{
    public function __construct(private TwoFactorService $twoFactorService) {}

    /**
     * Disable 2FA for the authenticated user.
     * Requires a valid TOTP code for security.
     *
     * POST /api/v1/auth/2fa/disable
     */
    public function __invoke(TwoFactorCodeRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $this->twoFactorService->isEnabled($user)) {
            return response()->json([
                'message' => 'L\'authentification 2FA n\'est pas activée.',
            ], 422);
        }

        if (! $this->twoFactorService->verify($user, $request->validated('code'))) {
            return response()->json([
                'message' => 'Code 2FA invalide.',
            ], 422);
        }

        $this->twoFactorService->disable($user);

        return response()->json([
            'message' => 'Authentification 2FA désactivée.',
        ]);
    }
}
