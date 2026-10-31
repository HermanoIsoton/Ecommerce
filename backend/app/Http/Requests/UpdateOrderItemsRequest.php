<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['sometimes', 'required', 'integer', 'exists:orders,id'],
            'product_id' => ['sometimes', 'nullable', 'integer', 'exists:products,id'],
            'product_name' => ['sometimes', 'required', 'string', 'max:255'],
            'unit_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'subtotal_cents' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }
}
