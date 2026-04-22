<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying.
     */
    public int $backoff = 5;

    public function __construct(
        public Message $message,
    ) {}

    public function handle(AiService $aiService): void
    {
        $aiService->processMessage($this->message);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessMessageJob failed', [
            'message_id'      => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'error'           => $exception->getMessage(),
        ]);
    }
}
