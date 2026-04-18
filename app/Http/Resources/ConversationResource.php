<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'status'       => $this->status->value,
            'channel'      => $this->channel->value,
            'rating'       => $this->rating,
            'summary'      => $this->summary,
            'closed_at'    => $this->closed_at?->toISOString(),
            'created_at'   => $this->created_at->toISOString(),
            'updated_at'   => $this->updated_at->toISOString(),
            'user'         => new UserResource($this->whenLoaded('user')),
            'expert'       => new ExpertResource($this->whenLoaded('expert')),
            'category'     => new CategoryResource($this->whenLoaded('category')),
            'last_message' => new MessageResource($this->whenLoaded('lastMessage')),
            'messages_count' => $this->whenCounted('messages'),
            'unread_count'   => $this->whenCounted('unreadMessages'),
        ];
    }
}
