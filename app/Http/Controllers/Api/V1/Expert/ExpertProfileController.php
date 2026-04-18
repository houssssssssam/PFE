<?php

namespace App\Http\Controllers\Api\V1\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Expert\UpdateExpertProfileRequest;
use App\Http\Resources\ExpertResource;
use App\Services\ExpertService;
use Illuminate\Http\JsonResponse;

class ExpertProfileController extends Controller
{
    public function __construct(private ExpertService $expertService) {}

    /**
     * Get the authenticated expert's profile.
     *
     * GET /api/v1/expert/profile
     */
    public function show(): JsonResponse
    {
        $expert = request()->user()->expert()->with(['category', 'documents', 'user'])->firstOrFail();

        return response()->json([
            'data' => new ExpertResource($expert),
        ]);
    }

    /**
     * Update the authenticated expert's profile.
     *
     * PUT /api/v1/expert/profile
     */
    public function update(UpdateExpertProfileRequest $request): JsonResponse
    {
        $expert = $request->user()->expert;

        if (! $expert) {
            return response()->json([
                'message' => 'Profil expert introuvable.',
            ], 404);
        }

        $expert = $this->expertService->updateProfile($expert, $request->validated());

        return response()->json([
            'message' => 'Profil expert mis à jour.',
            'data'    => new ExpertResource($expert),
        ]);
    }
}
