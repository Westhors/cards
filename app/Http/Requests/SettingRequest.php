<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'promotional_offer_image_one' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promotional_offer_image_two' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promotional_offer_image_three' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promotional_offer_image_four' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promotional_offer_image_five' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'title_one' => 'nullable|string',
            'title_two' => 'nullable|string',
            'title_three' => 'nullable|string',
            'title_four' => 'nullable|string',
            'title_five' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
        ];
    }
}







