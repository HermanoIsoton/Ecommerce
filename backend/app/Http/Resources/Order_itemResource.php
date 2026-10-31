<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Order_itemResource extends JsonResource
{
 
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'subtotal_cents' => $this->subtotal_cents,
        ];
    }
}
