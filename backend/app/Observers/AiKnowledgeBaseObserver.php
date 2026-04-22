<?php

namespace App\Observers;

use App\Jobs\GenerateEmbeddingJob;
use App\Models\AiKnowledgeBase;
use App\Services\VectorSearchService;

class AiKnowledgeBaseObserver
{
    public function __construct(private VectorSearchService $vectorSearch) {}

    public function created(AiKnowledgeBase $entry): void
    {
        GenerateEmbeddingJob::dispatch($entry);
    }

    public function updated(AiKnowledgeBase $entry): void
    {
        if ($entry->wasChanged(['question', 'answer'])) {
            GenerateEmbeddingJob::dispatch($entry);
        }
    }

    public function deleted(AiKnowledgeBase $entry): void
    {
        if ($entry->embedding_id) {
            $this->vectorSearch->deletePoint($entry->embedding_id);
        }
    }
}
