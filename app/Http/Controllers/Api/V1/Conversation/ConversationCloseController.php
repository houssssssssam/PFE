<?php

namespace App\Http\Controllers\Api\V1\Conversation;

use App\Http\Controllers\Controller;
use App\Jobs\SummarizeConversationJob;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationCloseController extends Controller
{
    public function __construct(private ConversationService $conversationService) {}

    /**
     * Close a conversation.
     *
     * PUT /api/v1/conversations/{conversation}/close
     */
    public function __invoke(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Owner, assigned expert, or admin can close
        $canClose = $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id)
            || $user->isAdmin();

        if (! $canClose) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if ($conversation->status->value === 'closed') {
            return response()->json([
                'message' => 'Cette conversation est déjà fermée.',
            ], 422);
        }

        $this->conversationService->close($conversation);

        // Trigger AI summary generation
        SummarizeConversationJob::dispatch($conversation);

        return response()->json([
            'message' => 'Conversation fermée.',
        ]);
    }
}
