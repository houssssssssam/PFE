<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OtpType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ResendOtpRequest;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;

class ResendOtpController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function __invoke(ResendOtpRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $type = OtpType::from($request->type);

        $otp = $this->otpService->generate($user, $type);

        SendEmailJob::dispatch('otp', $user, ['otp' => $otp->code, 'type' => $request->type]);

        return response()->json(['message' => 'Code envoyé. Vérifiez votre email.']);
    }
}
