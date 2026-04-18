<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AiLogResource;
use App\Models\AiLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAiLogController extends Controller
{
    /**
     * List AI interaction logs with filtering.
     *
     * GET /api/v1/admin/ai-logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = AiLog::query()->orderByDesc('created_at');

        if ($request->filled('conversation_id')) {
            $query->where('conversation_id', $request->integer('conversation_id'));
        }

        if ($request->filled('workflow')) {
            $query->where('workflow', $request->input('workflow'));
        }

        if ($request->filled('escalated')) {
            $query->where('escalated', $request->boolean('escalated'));
        }

        $logs = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => AiLogResource::collection($logs),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
            ],
        ]);
    }

    /**
     * Get AI usage statistics.
     *
     * GET /api/v1/admin/ai-logs/stats
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_interactions'  => AiLog::count(),
            'total_escalations'   => AiLog::where('escalated', true)->count(),
            'total_tokens_used'   => AiLog::sum('tokens_used'),
            'avg_confidence'      => round(AiLog::avg('confidence') ?? 0, 2),
            'by_workflow'         => AiLog::selectRaw('workflow, COUNT(*) as count, SUM(tokens_used) as tokens')
                ->groupBy('workflow')
                ->get()
                ->keyBy('workflow'),
        ];

        return response()->json(['data' => $stats]);
    }
}
