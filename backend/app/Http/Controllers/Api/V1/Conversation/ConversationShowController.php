<?php

namespace App\Http\Controllers\Api\V1\Conversation;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationShowController extends Controller
{
    /**
     * Show a single conversation with details.
     *
     * GET /api/v1/conversations/{conversation}
     */
    public function __invoke(Request $request, Conversation $conversation): JsonResponse
    {
        // Authorization check
        if (! $this->canAccess($request->user(), $conversation)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $conversation->load(['user', 'category', 'expert.user', 'review'])
            ->loadCount(['messages', 'unreadMessages']);

        return response()->json([
            'data' => new ConversationResource($conversation),
        ]);
    }

    private function canAccess($user, $conversation): bool
    {
        return $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id)
            || $user->isAdmin();
    }
}
