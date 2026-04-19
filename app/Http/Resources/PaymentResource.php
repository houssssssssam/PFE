<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'amount'                   => $this->amount,
            'currency'                 => $this->currency,
            'status'                   => $this->status->value,
            'provider'                 => $this->provider->value,
            'stripe_payment_intent_id' => $this->stripe_payment_intent_id,
            'cmi_order_id'             => $this->cmi_order_id,
            'paid_at'                  => $this->paid_at?->toISOString(),
            'created_at'               => $this->created_at?->toISOString(),
            'user'                     => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
            'expert'                   => $this->whenLoaded('expert', fn () => [
                'id'   => $this->expert->id,
                'name' => $this->expert->user?->name,
            ]),
            'conversation_id'          => $this->conversation_id,
        ];
    }
}
