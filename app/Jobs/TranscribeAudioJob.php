<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\N8nService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranscribeAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(private Message $message) {}

    public function handle(N8nService $n8nService): void
    {
        $n8nService->transcribe([
            'message_id'      => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'audio_url'       => $this->message->media_url,
            'language'        => $this->message->conversation?->user?->language ?? 'fr',
        ]);
    }
}
