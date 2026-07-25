<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles role check
    }

    public function rules(): array
    {
        return [
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'branch_id'      => ['required', 'exists:branches,id'],
        ];
    }
}
