<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $this->authService->sendPasswordResetOtp($user);

        return response()->json([
            'message' => 'Code de réinitialisation envoyé. Vérifiez votre email.',
        ]);
    }
}
