<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpertResource;
use App\Models\Expert;
use App\Enums\ExpertStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminExpertListController extends Controller
{
    /**
     * List experts pending validation.
     *
     * GET /api/v1/admin/experts/pending
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = Expert::with(['user', 'category', 'documents']);

        // Default to pending, but allow filtering by status
        $status = $request->input('status', 'pending');
        $query->where('status', $status);

        $experts = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ExpertResource::collection($experts),
            'meta' => [
                'current_page' => $experts->currentPage(),
                'last_page'    => $experts->lastPage(),
                'per_page'     => $experts->perPage(),
                'total'        => $experts->total(),
            ],
        ]);
    }
}
