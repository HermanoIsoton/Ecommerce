<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCartsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'token_hash' => ['sometimes', 'nullable', 'string', 'size:64',
                Rule::unique('carts', 'token_hash')->ignore($this->route('cart')),
            ],
        ];
    }
}
