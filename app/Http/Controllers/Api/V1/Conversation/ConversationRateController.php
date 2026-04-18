<?php

namespace App\Http\Controllers\Api\V1\Conversation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Conversation\RateConversationRequest;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\JsonResponse;

class ConversationRateController extends Controller
{
    public function __construct(private ConversationService $conversationService) {}

    /**
     * Rate a closed conversation (1-5) with optional comment.
     *
     * POST /api/v1/conversations/{conversation}/rate
     */
    public function __invoke(RateConversationRequest $request, Conversation $conversation): JsonResponse
    {
        if ($request->user()->id !== $conversation->user_id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if ($conversation->status->value !== 'closed') {
            return response()->json([
                'message' => 'La conversation doit être fermée pour être notée.',
            ], 422);
        }

        if ($conversation->rating) {
            return response()->json([
                'message' => 'Cette conversation a déjà été notée.',
            ], 422);
        }

        $this->conversationService->rate(
            $conversation,
            $request->validated('rating'),
            $request->validated('comment')
        );

        return response()->json([
            'message' => 'Merci pour votre évaluation.',
        ]);
    }
}
