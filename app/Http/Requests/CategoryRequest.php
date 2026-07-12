<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . optional($this->route('category'))->id,
            'parent_id' => 'nullable|exists:categories,id',
            // 'image' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ];
    }
}

