<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'in:pendente,pago,enviado,entregue,cancelado'],
            'subtotal_cents' => ['required', 'numeric', 'min:0'],
            'shipping_cents' => ['required', 'numeric', 'min:0'],
            'total_cents' => ['required', 'numeric', 'min:0'],
            'ship_recipient' => ['required', 'string', 'max:255'],
            'ship_cep' => ['required', 'string', 'regex:/^\d{5}-?\d{3}$/'],
            'ship_street' => ['required', 'string', 'max:255'],
            'ship_number' => ['required', 'string', 'max:20'],
            'ship_complement' => ['required', 'string', 'max:20'],
            'ship_district' => ['required', 'string', 'max:255'],
            'ship_city' => ['required', 'string', 'max:255'],
            'ship_state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'paid_at' => ['nullable', 'date'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'cancelled_at' => ['nullable', 'date'],
        ];
    }
}
