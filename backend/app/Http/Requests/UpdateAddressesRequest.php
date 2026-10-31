<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'recipient' => ['sometimes', 'required', 'string', 'max:255'],
            'cep' => ['sometimes', 'required', 'string', 'regex:/^\d{5}-?\d{3}$/'],
            'street' => ['sometimes', 'required', 'string', 'max:255'],
            'number' => ['sometimes', 'required', 'string', 'max:255'],
            'complement' => ['sometimes', 'nullable', 'string', 'max:255'],
            'district' => ['sometimes', 'required', 'string', 'max:255'],
            'city' => ['sometimes', 'required', 'string', 'max:255'],
            'state' => ['sometimes', 'required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
        ];
    }
}
