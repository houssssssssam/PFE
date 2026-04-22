<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'email'                   => $this->email,
            'phone'                   => $this->phone,
            'role'                    => $this->role->value,
            'avatar_url'              => $this->avatar_url,
            'language'                => $this->language,
            'is_active'               => $this->is_active,
            'email_verified_at'       => $this->email_verified_at?->toISOString(),
            'two_factor_enabled'      => $this->two_factor_confirmed_at !== null,
            'created_at'              => $this->created_at->toISOString(),
        ];
    }
}
