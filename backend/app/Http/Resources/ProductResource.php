<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
            'model_year' => $this->model_year,
            'seating_capacity' => $this->seating_capacity,
            'doors_count' => $this->doors_count,
            'trunk_capacity_l' => $this->trunk_capacity_l,
            'type' => $this->type,
            'power' => $this->power,
            'range_km' => $this->range_km,
            'top_speed_kmh' => $this->top_speed_kmh,
            'weight_kg' => $this->weight_kg,
            'charge_time_h' => $this->charge_time_h,
            'battery_capaciy' => $this->battery_capaciy,
            'max_charge_power_kw' => $this->max_charge_power_kw,
            'battery_voltage_v' => $this->battery_voltage_v,
            'is_battery_removable' => $this->is_battery_removable,
            'warranty_months' => $this->warranty_months,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
