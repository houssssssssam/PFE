<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpertResource;
use App\Models\Expert;
use Illuminate\Http\JsonResponse;

class ExpertShowController extends Controller
{
    /**
     * Show a single expert's public profile.
     *
     * GET /api/v1/experts/{expert}
     */
    public function __invoke(Expert $expert): JsonResponse
    {
        $expert->load(['user', 'category']);

        return response()->json([
            'data' => new ExpertResource($expert),
        ]);
    }
}
