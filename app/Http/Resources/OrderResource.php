<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'subtotal_cents' => $this->subtotal_cents,
            'shipping_cents' => $this->shipping_cents,
            'total_cents' => $this->total_cents,
            'ship_recipient' => $this->ship_recipient,
            'ship_cep' => $this->ship_cep,
            'ship_street' => $this->ship_street,
            'ship_number' => $this->ship_number,
            'ship_complement' => $this->ship_complement,
            'ship_district' => $this->ship_district,
            'ship_city' => $this->ship_city,
            'ship_state' => $this->ship_state,
            'paid_at' => $this->paid_at,
            'shipped_at' => $this->shipped_at,
            'delivered_at' => $this->delivered_at,
            'cancelled_at' => $this->cancelled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
