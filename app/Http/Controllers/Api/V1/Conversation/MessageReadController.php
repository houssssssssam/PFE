<?php

namespace App\Http\Controllers\Api\V1\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageReadController extends Controller
{
    public function __construct(private MessageService $messageService) {}

    /**
     * Mark a single message as read.
     *
     * PUT /api/v1/conversations/{conversation}/messages/{message}/read
     */
    public function markOne(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $user = $request->user();

        if (! $this->canAccess($user, $conversation)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if ($message->conversation_id !== $conversation->id) {
            return response()->json(['message' => 'Message introuvable dans cette conversation.'], 404);
        }

        $this->messageService->markAsRead($message);

        return response()->json([
            'message' => 'Message marqué comme lu.',
        ]);
    }

    /**
     * Mark all messages in a conversation as read.
     *
     * PUT /api/v1/conversations/{conversation}/messages/read-all
     */
    public function markAll(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (! $this->canAccess($user, $conversation)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $count = $this->messageService->markAllAsRead($conversation, $user);

        return response()->json([
            'message' => "{$count} message(s) marqué(s) comme lu(s).",
        ]);
    }

    private function canAccess($user, $conversation): bool
    {
        return $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id)
            || $user->isAdmin();
    }
}
