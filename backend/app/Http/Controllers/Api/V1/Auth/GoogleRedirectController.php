<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class GoogleRedirectController extends Controller
{
    /**
     * Return the Google OAuth redirect URL.
     *
     * GET /api/v1/auth/google/redirect
     */
    public function __invoke(): JsonResponse
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }
}
