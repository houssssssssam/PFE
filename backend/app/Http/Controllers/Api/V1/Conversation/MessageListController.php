<?php

namespace App\Http\Controllers\Api\V1\Conversation;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageListController extends Controller
{
    /**
     * List messages in a conversation with pagination.
     *
     * GET /api/v1/conversations/{conversation}/messages
     */
    public function __invoke(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (! $this->canAccess($user, $conversation)) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $messages = $conversation->messages()
            ->orderBy('created_at', $request->input('order', 'asc'))
            ->paginate($request->integer('per_page', 50));

        return response()->json([
            'data' => MessageResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'per_page'     => $messages->perPage(),
                'total'        => $messages->total(),
            ],
        ]);
    }

    private function canAccess($user, $conversation): bool
    {
        return $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id)
            || $user->isAdmin();
    }
}
