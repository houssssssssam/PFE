<?php

namespace App\Http\Controllers\Api\V1\Conversation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Conversation\SendAudioRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;

class MessageAudioController extends Controller
{
    public function __construct(private MessageService $messageService) {}

    /**
     * Send an audio message in a conversation.
     *
     * POST /api/v1/conversations/{conversation}/messages/audio
     */
    public function __invoke(SendAudioRequest $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (! $this->canSend($user, $conversation)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if ($conversation->status->value === 'closed') {
            return response()->json([
                'message' => 'Impossible d\'envoyer un message dans une conversation fermée.',
            ], 422);
        }

        $message = $this->messageService->sendAudio(
            $conversation,
            $user,
            $request->file('audio')
        );

        return response()->json([
            'data' => new MessageResource($message),
        ], 201);
    }

    private function canSend($user, $conversation): bool
    {
        return $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id);
    }
}
