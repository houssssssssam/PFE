<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpertDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'type'          => $this->type->value,
            'file_url'      => $this->file_url,
            'original_name' => $this->original_name,
            'verified'      => $this->verified,
            'created_at'    => $this->created_at->toISOString(),
        ];
    }
}
