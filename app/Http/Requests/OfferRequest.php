<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'card_id' => 'nullable|exists:cards,id',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ];
    }
}
