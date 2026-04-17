<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Models\OtpCode;
use App\Models\User;

class OtpService
{
    public function generate(User $user, OtpType $type): OtpCode
    {
        // Invalidate any previous unused OTPs of the same type
        OtpCode::where('user_id', $user->id)
            ->where('type', $type->value)
            ->whereNull('used_at')
            ->delete();

        return OtpCode::create([
            'user_id'    => $user->id,
            'code'       => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'type'       => $type->value,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function verify(User $user, OtpType $type, string $code): bool
    {
        $otp = OtpCode::where('user_id', $user->id)
            ->where('type', $type->value)
            ->where('code', $code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }
}
