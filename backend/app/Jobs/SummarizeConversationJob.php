<?php

namespace App\Jobs;

use App\Models\Conversation;
use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SummarizeConversationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 10;

    public function __construct(
        public Conversation $conversation,
    ) {}

    public function handle(AiService $aiService): void
    {
        $aiService->summarizeConversation($this->conversation);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SummarizeConversationJob failed', [
            'conversation_id' => $this->conversation->id,
            'error'           => $exception->getMessage(),
        ]);
    }
}
