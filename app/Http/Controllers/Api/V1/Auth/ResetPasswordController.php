<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OtpType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    public function __construct(
        private OtpService $otpService,
        private AuthService $authService
    ) {}

    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (! $this->otpService->verify($user, OtpType::PasswordReset, $request->code)) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        $this->authService->resetPassword($user, $request->password);

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès.']);
    }
}
