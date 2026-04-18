<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'conversation_id' => $this->conversation_id,
            'message_id'      => $this->message_id,
            'workflow'        => $this->workflow,
            'model'           => $this->model,
            'confidence'      => $this->confidence,
            'tokens_used'     => $this->tokens_used,
            'duration_ms'     => $this->duration_ms,
            'escalated'       => $this->escalated,
            'created_at'      => $this->created_at->toISOString(),
        ];
    }
}
