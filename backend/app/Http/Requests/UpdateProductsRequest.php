<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255',
                Rule::unique('products', 'slug')->ignore($this->route('product')),
            ],
            'description' => ['sometimes', 'required', 'string', 'max:5000'],
            'image_url' => ['sometimes', 'required', 'string', 'url', 'max:255', 'ends_with:.jpg,.jpeg,.webp'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'model_year' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'seating_capacity' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'doors_count' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'trunk_capacity_l' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'type' => ['sometimes', 'required', 'in:autopropelido,ciclomotor'],
            'power' => ['sometimes', 'required', 'integer', 'min:1'],
            'range_km' => ['sometimes', 'required', 'integer', 'min:1'],
            'top_speed_kmh' => ['sometimes', 'required', 'integer', 'min:1'],
            'weight_kg' => ['sometimes', 'required', 'numeric', 'min:0'],
            'charge_time_h' => ['sometimes', 'required', 'numeric', 'min:0'],
            'battery_capaciy' => ['sometimes', 'required', 'numeric', 'min:0'],
            'max_charge_power_kw' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'battery_voltage_v' => ['sometimes', 'required', 'numeric', 'min:0'],
            'is_battery_removable' => ['sometimes', 'boolean'],
            'warranty_months' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}
