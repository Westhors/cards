<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0|max:120',
            'gender' => 'nullable|string|in:male,female,other',
            'country' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            // 'active' => 'nullable|boolean',
            'is_refused' => 'nullable|boolean',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user), // assuming route binding
            ],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }
}







