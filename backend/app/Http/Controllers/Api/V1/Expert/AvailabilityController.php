<?php

namespace App\Http\Controllers\Api\V1\Expert;

use App\Http\Controllers\Controller;
use App\Services\ExpertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(private ExpertService $expertService) {}

    /**
     * Toggle expert availability.
     *
     * PUT /api/v1/expert/availability
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'is_available' => ['required', 'boolean'],
        ]);

        $expert = $request->user()->expert;

        if (! $expert) {
            return response()->json([
                'message' => 'Profil expert introuvable.',
            ], 404);
        }

        $this->expertService->toggleAvailability($expert, $request->boolean('is_available'));

        return response()->json([
            'message' => $request->boolean('is_available')
                ? 'Vous êtes maintenant disponible.'
                : 'Vous êtes maintenant indisponible.',
            'data' => [
                'is_available' => $expert->fresh()->is_available,
            ],
        ]);
    }
}
