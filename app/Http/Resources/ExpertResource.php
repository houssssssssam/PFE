<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user'            => new UserResource($this->whenLoaded('user')),
            'category'        => new CategoryResource($this->whenLoaded('category')),
            'bio'             => $this->bio,
            'certifications'  => $this->certifications,
            'hourly_rate'     => $this->hourly_rate,
            'rating_avg'      => $this->rating_avg,
            'total_reviews'   => $this->total_reviews,
            'is_available'    => $this->is_available,
            'status'          => $this->status->value,
            'validated_at'    => $this->validated_at?->toISOString(),
            'documents'       => ExpertDocumentResource::collection($this->whenLoaded('documents')),
            'created_at'      => $this->created_at->toISOString(),
        ];
    }
}
