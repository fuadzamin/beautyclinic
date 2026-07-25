<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|integer|min:0',
            'category'       => 'required|in:serum,sunscreen,moisturizer,cleanser,acne_treatment,mask,body_care,soap',
            'image'          => 'nullable|image|max:2048',
            'image_url'      => 'nullable|string',
            'ingredients'    => 'nullable|string',
            'volume'         => 'nullable|string|max:50',
            'is_active'      => 'boolean',
        ];
    }
}
