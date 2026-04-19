<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminConversationController extends Controller
{
    /**
     * GET /api/v1/admin/conversations
     */
    public function __invoke(Request $request): JsonResponse
    {
        $conversations = Conversation::query()
            ->with(['user', 'expert.user', 'category'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => ConversationResource::collection($conversations),
            'meta' => [
                'total'        => $conversations->total(),
                'current_page' => $conversations->currentPage(),
                'last_page'    => $conversations->lastPage(),
            ],
        ]);
    }
}
