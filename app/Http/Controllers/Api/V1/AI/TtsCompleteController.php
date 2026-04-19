<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use App\Events\AIResponseReady;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TtsCompleteController extends Controller
{
    /**
     * POST /api/v1/ai/tts-complete
     * Called by n8n after ElevenLabs TTS audio is generated.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->header('X-N8N-Secret') !== config('services.n8n.secret')) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'message_id' => ['required', 'exists:messages,id'],
            'audio_url'  => ['required', 'string'],
        ]);

        $message = Message::findOrFail($request->message_id);
        $message->update(['media_url' => $request->audio_url]);

        event(new AIResponseReady($message->fresh()));

        return response()->json(['message' => 'TTS complete.']);
    }
}
