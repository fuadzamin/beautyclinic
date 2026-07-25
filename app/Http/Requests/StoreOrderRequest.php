<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'customer_name'          => 'required|string|max:100',
            'customer_phone'         => ['required', 'string', 'regex:/^62\d{9,12}$/'],
            'notes'                  => 'nullable|string|max:500',
            'delivery_method'        => 'required|in:pickup,shipping',
            'branch_id'              => 'required_if:delivery_method,pickup|nullable|exists:branches,id',
            'shipping_address'       => 'required_if:delivery_method,shipping|nullable|string',
        ];
    }
}
