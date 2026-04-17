<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OtpType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;

class VerifyEmailController extends Controller
{
    public function __construct(
        private OtpService $otpService,
        private AuthService $authService
    ) {}

    public function __invoke(VerifyEmailRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email déjà vérifié.'], 422);
        }

        if (! $this->otpService->verify($user, OtpType::EmailVerification, $request->code)) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        $user->update(['email_verified_at' => now()]);

        $tokens = $this->authService->createTokenPair($user);

        return response()->json([
            'message' => 'Email vérifié avec succès.',
            'data'    => [
                'user'  => new UserResource($user->fresh()),
                'token' => $tokens,
            ],
        ])->withCookie(
            cookie('refresh_token', $tokens['refresh_token'], 60 * 24 * 30,
                secure: app()->isProduction(), httpOnly: true, sameSite: 'Strict')
        );
    }
}
