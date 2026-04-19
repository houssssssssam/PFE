<?php

namespace App\Jobs;

use App\Models\AiKnowledgeBase;
use App\Services\VectorSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateEmbeddingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(private AiKnowledgeBase $entry) {}

    public function handle(VectorSearchService $vectorSearch): void
    {
        try {
            $text      = $this->entry->question . ' ' . $this->entry->answer;
            $embedding = $vectorSearch->embedText($text);

            $vectorSearch->upsert($this->entry->id, $embedding);

            $this->entry->update(['embedding_id' => (string) $this->entry->id]);
        } catch (\Throwable $e) {
            Log::error('GenerateEmbeddingJob failed', [
                'entry_id' => $this->entry->id,
                'error'    => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
