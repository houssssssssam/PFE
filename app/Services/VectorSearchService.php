<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VectorSearchService
{
    private string $host;
    private string $collection;

    public function __construct()
    {
        $this->host       = config('services.qdrant.host');
        $this->collection = config('services.qdrant.collection');
    }

    /**
     * Generate an embedding vector for the given text using OpenAI.
     */
    public function embedText(string $text): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/embeddings', [
            'model' => config('services.openai.embedding_model', 'text-embedding-3-small'),
            'input' => $text,
        ]);

        return $response->json('data.0.embedding', []);
    }

    /**
     * Search Qdrant for the most relevant knowledge entries.
     */
    public function search(array $embedding, ?string $categorySlug = null, int $limit = 5): array
    {
        $payload = [
            'vector' => $embedding,
            'limit'  => $limit,
            'with_payload' => true,
        ];

        if ($categorySlug) {
            $payload['filter'] = [
                'must' => [
                    ['key' => 'category', 'match' => ['value' => $categorySlug]],
                ],
            ];
        }

        try {
            $response = Http::post("{$this->host}/collections/{$this->collection}/points/search", $payload);

            return $response->json('result', []);
        } catch (\Throwable $e) {
            Log::error('Qdrant search failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Upsert a knowledge base entry into Qdrant.
     */
    public function upsert(int $knowledgeId, array $embedding): void
    {
        $this->ensureCollection();

        Http::put("{$this->host}/collections/{$this->collection}/points", [
            'points' => [[
                'id'      => $knowledgeId,
                'vector'  => $embedding,
                'payload' => ['knowledge_id' => $knowledgeId],
            ]],
        ]);
    }

    /**
     * Delete a point from Qdrant.
     */
    public function deletePoint(string $pointId): void
    {
        try {
            Http::post("{$this->host}/collections/{$this->collection}/points/delete", [
                'points' => [(int) $pointId],
            ]);
        } catch (\Throwable $e) {
            Log::error('Qdrant deletePoint failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ensure the Qdrant collection exists, create it if not.
     */
    public function ensureCollection(): void
    {
        try {
            $response = Http::get("{$this->host}/collections/{$this->collection}");

            if ($response->status() === 404) {
                Http::put("{$this->host}/collections/{$this->collection}", [
                    'vectors' => [
                        'size'     => config('services.qdrant.vector_size', 1536),
                        'distance' => 'Cosine',
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Qdrant ensureCollection failed', ['error' => $e->getMessage()]);
        }
    }
}
