<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Expert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertReviewsController extends Controller
{
    /**
     * List reviews for a given expert.
     *
     * GET /api/v1/experts/{expert}/reviews
     */
    public function __invoke(Request $request, Expert $expert): JsonResponse
    {
        $reviews = $expert->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page'    => $reviews->lastPage(),
                'per_page'     => $reviews->perPage(),
                'total'        => $reviews->total(),
                'rating_avg'   => $expert->rating_avg,
                'total_reviews' => $expert->total_reviews,
            ],
        ]);
    }
}
