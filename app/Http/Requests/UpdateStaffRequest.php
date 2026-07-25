<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => 'sometimes|nullable|exists:branches,id',
            'name'      => 'sometimes|string|max:100',
            'email'     => 'sometimes|email|unique:staff,email,' . $this->route('staff')?->id,
            'phone'     => 'sometimes|nullable|string|max:20',
            'password'  => ['sometimes', 'nullable', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/'],
            'role'      => 'sometimes|in:owner,branch_manager,admin_klinik,admin_produk',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
