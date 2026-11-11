<?php

namespace App\Http\Resources;

use App\Models\Cart_item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalCents = 0;
        $totalUnits = 0;
        $items = $this->whenLoaded('items', function () use (&$totalCents, &$totalUnits) {
            return $this->items->map(function (Cart_item $item) use (&$totalCents, &$totalUnits): array {
                $product = $item->product;
                $quantity = (int) $item->quantity;
                $unitPriceCents = (int) round((float) $product->price * 100);
                $subtotalCents = $unitPriceCents * $quantity;
                $totalCents += $subtotalCents;
                $totalUnits += $quantity;
                $isAvailable = (bool) $product->is_active && (int) $product->stock >= $quantity;

                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPriceCents / 100,
                    'subtotal' => $subtotalCents / 100,
                    'is_available' => $isAvailable,
                    'availability_message' => ! $product->is_active
                        ? 'Produto indisponível'
                        : ((int) $product->stock < $quantity ? 'Estoque insuficiente' : null),
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image_url' => $product->image_url,
                        'price' => $unitPriceCents / 100,
                        'stock' => (int) $product->stock,
                        'is_active' => (bool) $product->is_active,
                        'type' => $product->type,
                        'range_km' => $product->range_km,
                        'top_speed_kmh' => $product->top_speed_kmh,
                    ],
                ];
            })->values();
        });

        return [
            'id' => $this->id,
            'items' => $items,
            'total_units' => $this->whenLoaded('items', fn () => $totalUnits),
            'subtotal' => $this->whenLoaded('items', fn () => $totalCents / 100),
            'total' => $this->whenLoaded('items', fn () => $totalCents / 100),
        ];
    }
}