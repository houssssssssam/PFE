<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\RejectExpertRequest;
use App\Http\Resources\ExpertResource;
use App\Models\Expert;
use App\Services\ExpertService;
use Illuminate\Http\JsonResponse;

class AdminExpertRejectController extends Controller
{
    public function __construct(private ExpertService $expertService) {}

    /**
     * Reject an expert application.
     *
     * PUT /api/v1/admin/experts/{expert}/reject
     */
    public function __invoke(RejectExpertRequest $request, Expert $expert): JsonResponse
    {
        if ($expert->status->value !== 'pending') {
            return response()->json([
                'message' => 'Cette candidature a déjà été traitée.',
            ], 422);
        }

        $expert = $this->expertService->reject(
            $expert,
            $request->user(),
            $request->validated('reason')
        );

        return response()->json([
            'message' => 'Candidature expert rejetée.',
            'data'    => new ExpertResource($expert),
        ]);
    }
}
