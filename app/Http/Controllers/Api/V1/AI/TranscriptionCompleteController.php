<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMessageJob;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranscriptionCompleteController extends Controller
{
    /**
     * POST /api/v1/ai/transcription-complete
     * Called by n8n after Whisper transcription is done.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->header('X-N8N-Secret') !== config('services.n8n.secret')) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'message_id'    => ['required', 'exists:messages,id'],
            'transcription' => ['required', 'string'],
        ]);

        $message = Message::findOrFail($request->message_id);
        $message->update(['transcription' => $request->transcription]);

        ProcessMessageJob::dispatch($message);

        return response()->json(['message' => 'Transcription saved.']);
    }
}
