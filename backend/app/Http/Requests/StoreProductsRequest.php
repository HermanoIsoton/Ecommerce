<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['required', 'string', 'max:5000'],
            'image_url' => ['required', 'string', 'url', 'max:255', 'ends_with:.jpg,.jpeg,.webp'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'model_year' => ['nullable', 'integer', 'min:0'],
            'seating_capacity' => ['nullable', 'integer', 'min:1'],
            'doors_count' => ['nullable', 'integer', 'min:1'],
            'trunk_capacity_l' => ['nullable', 'integer', 'min:0'],
            'type' => ['required', 'in:autopropelido,ciclomotor'],
            'power' => ['required', 'integer', 'min:1'],
            'range_km' => ['required', 'integer', 'min:1'],
            'top_speed_kmh' => ['required', 'integer', 'min:1'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
            'charge_time_h' => ['required', 'numeric', 'min:0'],
            'battery_capaciy' => ['required', 'numeric', 'min:0'],
            'max_charge_power_kw' => ['nullable', 'integer', 'min:0'],
            'battery_voltage_v' => ['required', 'numeric', 'min:0'],
            'is_battery_removable' => ['sometimes', 'boolean'],
            'warranty_months' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
