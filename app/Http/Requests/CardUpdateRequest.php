<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardUpdateRequest extends FormRequest
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
            'type' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type_silicone' => 'nullable|string|max:255',
            'hardness' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
            'time_in_ear' => 'nullable|string|max:255',
            'end_curing' => 'nullable|string|max:255',
            'viscosity' => 'nullable|string|max:255',
            'color' => 'nullable',
            'packaging' => 'nullable|string|max:255',
            'item_number' => 'nullable|string|max:255',
            'mix_gun' => 'nullable|string|max:255',
            'mix_canules' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'discount' => 'nullable|string',
            'old_price' => 'nullable',
            'currency' => 'nullable|string|max:10',
            'link_video' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:categories,id',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'active' => 'nullable|boolean',
            'free_delevery' => 'nullable',
            'one_year_warranty' => 'nullable',
        ];
    }
}
