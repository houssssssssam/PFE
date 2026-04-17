<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(private OtpService $otpService) {}

    public function register(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'language' => $data['language'] ?? 'fr',
        ]);

        $otp = $this->otpService->generate($user, OtpType::EmailVerification);

        SendEmailJob::dispatch('otp', $user, ['otp' => $otp->code, 'type' => 'verification']);

        return $user;
    }

    public function createTokenPair(User $user): array
    {
        // Revoke all existing tokens on fresh login
        $user->tokens()->delete();

        $accessToken = $user->createToken(
            'access_token',
            ['*'],
            now()->addMinutes(config('sanctum.expiration', 15))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            ['refresh'],
            now()->addDays(30)
        );

        return [
            'access_token'  => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type'    => 'Bearer',
            'expires_in'    => config('sanctum.expiration', 15) * 60,
        ];
    }

    public function sendPasswordResetOtp(User $user): void
    {
        $otp = $this->otpService->generate($user, OtpType::PasswordReset);
        SendEmailJob::dispatch('otp', $user, ['otp' => $otp->code, 'type' => 'password_reset']);
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
        $user->tokens()->delete();
    }
}
