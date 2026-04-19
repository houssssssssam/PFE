<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KnowledgeBaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'category'     => new CategoryResource($this->whenLoaded('category')),
            'question'     => $this->question,
            'answer'       => $this->answer,
            'keywords'     => $this->keywords,
            'language'     => $this->language,
            'embedding_id' => $this->embedding_id,
            'is_active'    => $this->is_active,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
