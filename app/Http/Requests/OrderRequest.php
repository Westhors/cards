<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'apartment' => 'nullable|string',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'payment_method' => 'required|string|in:card,paypal,cash',
            'promo_code' => 'nullable|string|max:50',
            'payment_status' => 'nullable|string',

            'payment_type' => 'nullable|in:cash,installment',
            'installment_months' => 'nullable|integer',
            'increase_rate' => 'nullable|numeric', // مثال: 0.15
            'total_amount' => 'nullable|numeric', // مثال: 0.15

            'cards' => 'required|array|min:1',
            'cards.*.id' => 'required|exists:cards,id',
            'cards.*.qty' => 'required|integer|min:1',
            'cards.*.color' => 'nullable|string|max:50',
        ];
    }
}


