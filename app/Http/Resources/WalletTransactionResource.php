<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type->value,
            'amount'      => $this->amount,
            'description' => $this->description,
            'reference'   => $this->reference,
            'created_at'  => $this->created_at->toISOString(),
        ];
    }
}
