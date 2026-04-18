<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'balance'          => $this->balance,
            'total_earned'     => $this->total_earned,
            'total_withdrawn'  => $this->total_withdrawn,
            'transactions'     => WalletTransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
