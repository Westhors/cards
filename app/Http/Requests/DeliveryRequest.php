<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
$id = optional($this->route('man_delivery'))->id;
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:deliveries,name,' . $id,
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:deliveries,email,' . $id,
            ],
            'password' => 'required|string|max:255',
            'phone' => 'nullable|string',
        ];
    }
}
