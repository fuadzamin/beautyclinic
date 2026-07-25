<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'sometimes|string|max:255',
            'description'    => 'sometimes|nullable|string',
            'price'          => 'sometimes|integer|min:0',
            'category'       => 'sometimes|in:serum,sunscreen,moisturizer,cleanser,acne_treatment,mask,body_care,soap',
            'image'          => 'sometimes|nullable|image|max:2048',
            'image_url'      => 'sometimes|nullable|string',
            'ingredients'    => 'sometimes|nullable|string',
            'volume'         => 'sometimes|nullable|string|max:50',
            'is_active'      => 'sometimes|boolean',
        ];
    }
}
