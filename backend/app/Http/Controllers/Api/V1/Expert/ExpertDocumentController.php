<?php

namespace App\Http\Controllers\Api\V1\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Expert\UploadDocumentRequest;
use App\Http\Resources\ExpertDocumentResource;
use App\Models\ExpertDocument;
use App\Services\ExpertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertDocumentController extends Controller
{
    public function __construct(private ExpertService $expertService) {}

    /**
     * List expert's documents.
     *
     * GET /api/v1/expert/documents
     */
    public function index(Request $request): JsonResponse
    {
        $expert = $request->user()->expert;

        if (! $expert) {
            return response()->json(['message' => 'Profil expert introuvable.'], 404);
        }

        return response()->json([
            'data' => ExpertDocumentResource::collection($expert->documents),
        ]);
    }

    /**
     * Upload a new document.
     *
     * POST /api/v1/expert/documents
     */
    public function store(UploadDocumentRequest $request): JsonResponse
    {
        $expert = $request->user()->expert;

        if (! $expert) {
            return response()->json(['message' => 'Profil expert introuvable.'], 404);
        }

        $document = $this->expertService->uploadDocument(
            $expert,
            $request->file('file'),
            $request->validated('type')
        );

        return response()->json([
            'message' => 'Document téléchargé avec succès.',
            'data'    => new ExpertDocumentResource($document),
        ], 201);
    }

    /**
     * Delete a document.
     *
     * DELETE /api/v1/expert/documents/{document}
     */
    public function destroy(Request $request, ExpertDocument $document): JsonResponse
    {
        $expert = $request->user()->expert;

        if (! $expert || $document->expert_id !== $expert->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $this->expertService->deleteDocument($document);

        return response()->json([
            'message' => 'Document supprimé.',
        ]);
    }
}
