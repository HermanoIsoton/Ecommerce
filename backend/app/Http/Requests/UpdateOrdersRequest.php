<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'required', 'in:pendente,pago,enviado,entregue,cancelado'],
            'subtotal_cents' => ['sometimes', 'required', 'numeric', 'min:0'],
            'shipping_cents' => ['sometimes', 'required', 'numeric', 'min:0'],
            'total_cents' => ['sometimes', 'required', 'numeric', 'min:0'],
            'ship_recipient' => ['sometimes', 'required', 'string', 'max:255'],
            'ship_cep' => ['sometimes', 'required', 'string', 'regex:/^\d{5}-?\d{3}$/'],
            'ship_street' => ['sometimes', 'required', 'string', 'max:255'],
            'ship_number' => ['sometimes', 'required', 'string', 'max:20'],
            'ship_complement' => ['sometimes', 'required', 'string', 'max:20'],
            'ship_district' => ['sometimes', 'required', 'string', 'max:255'],
            'ship_city' => ['sometimes', 'required', 'string', 'max:255'],
            'ship_state' => ['sometimes', 'required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'paid_at' => ['sometimes', 'nullable', 'date'],
            'shipped_at' => ['sometimes', 'nullable', 'date'],
            'delivered_at' => ['sometimes', 'nullable', 'date'],
            'cancelled_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
