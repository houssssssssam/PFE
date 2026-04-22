<?php

namespace App\Http\Controllers\Api\V1\Conversation;

use App\Events\UserTyping;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypingController extends Controller
{
    /**
     * Broadcast a typing indicator.
     *
     * POST /api/v1/conversations/{conversation}/typing
     */
    public function __invoke(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        $canType = $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id);

        if (! $canType) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        event(new UserTyping(
            conversationId: $conversation->id,
            userId: $user->id,
            userName: $user->name,
            isTyping: $request->boolean('is_typing', true),
        ));

        return response()->json(['message' => 'ok']);
    }
}
