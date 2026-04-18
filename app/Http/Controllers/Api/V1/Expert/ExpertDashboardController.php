<?php

namespace App\Http\Controllers\Api\V1\Expert;

use App\Http\Controllers\Controller;
use App\Services\ExpertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertDashboardController extends Controller
{
    public function __construct(private ExpertService $expertService) {}

    /**
     * Get expert dashboard statistics.
     *
     * GET /api/v1/expert/dashboard
     */
    public function __invoke(Request $request): JsonResponse
    {
        $expert = $request->user()->expert;

        if (! $expert) {
            return response()->json([
                'message' => 'Profil expert introuvable.',
            ], 404);
        }

        $stats = $this->expertService->getDashboardStats($expert);

        return response()->json([
            'data' => $stats,
        ]);
    }
}
