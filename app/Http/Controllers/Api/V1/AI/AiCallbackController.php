<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use App\Services\AiService;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiCallbackController extends Controller
{
    public function __construct(private AiService $aiService) {}

    /**
     * POST /api/v1/ai/callback
     * Generic callback from n8n for AI workflow results.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->header('X-N8N-Secret') !== config('services.n8n.secret')) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return response()->json(['message' => 'Received.']);
    }
}
