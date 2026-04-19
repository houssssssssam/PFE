<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\KnowledgeBaseRequest;
use App\Http\Resources\KnowledgeBaseResource;
use App\Models\AiKnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminKnowledgeBaseController extends Controller
{
    /**
     * GET /api/v1/admin/knowledge
     */
    public function index(Request $request): JsonResponse
    {
        $entries = AiKnowledgeBase::query()
            ->with('category')
            ->when($request->search, fn ($q) => $q->where('question', 'like', "%{$request->search}%"))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->language, fn ($q) => $q->where('language', $request->language))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => KnowledgeBaseResource::collection($entries),
            'meta' => [
                'total'        => $entries->total(),
                'current_page' => $entries->currentPage(),
                'last_page'    => $entries->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/v1/admin/knowledge
     */
    public function store(KnowledgeBaseRequest $request): JsonResponse
    {
        $entry = AiKnowledgeBase::create($request->validated());

        return response()->json([
            'message' => 'Entrée créée.',
            'data'    => new KnowledgeBaseResource($entry->load('category')),
        ], 201);
    }

    /**
     * PUT /api/v1/admin/knowledge/{entry}
     */
    public function update(KnowledgeBaseRequest $request, AiKnowledgeBase $knowledge): JsonResponse
    {
        $knowledge->update($request->validated());

        return response()->json([
            'message' => 'Entrée mise à jour.',
            'data'    => new KnowledgeBaseResource($knowledge->fresh('category')),
        ]);
    }

    /**
     * DELETE /api/v1/admin/knowledge/{entry}
     */
    public function destroy(AiKnowledgeBase $knowledge): JsonResponse
    {
        $knowledge->delete();

        return response()->json(['message' => 'Entrée supprimée.']);
    }
}
