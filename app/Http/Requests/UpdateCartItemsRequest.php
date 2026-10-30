<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_id' => ['sometimes', 'required', 'integer', 'exists:carts,id'],
            'product_id' => ['sometimes', 'required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'required', 'numeric', 'gt:0'],
        ];
    }
}
