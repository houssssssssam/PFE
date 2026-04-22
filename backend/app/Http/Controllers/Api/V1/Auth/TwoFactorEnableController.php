<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoFactorEnableController extends Controller
{
    public function __construct(private TwoFactorService $twoFactorService) {}

    /**
     * Enable 2FA for the authenticated user.
     * Returns the TOTP secret and QR code URL for scanning.
     * User must still confirm with a code via /2fa/confirm.
     *
     * POST /api/v1/auth/2fa/enable
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($this->twoFactorService->isEnabled($user)) {
            return response()->json([
                'message' => 'L\'authentification 2FA est déjà activée.',
            ], 422);
        }

        $result = $this->twoFactorService->enable($user);

        return response()->json([
            'message'      => 'Scannez le QR code avec votre application d\'authentification.',
            'data'         => [
                'secret'       => $result['secret'],
                'qr_code_url'  => $result['qr_code_url'],
            ],
        ]);
    }
}
