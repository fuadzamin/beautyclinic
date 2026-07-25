<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id'=> 'nullable|exists:branches,id',
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:staff,email',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/'],
            'role'     => 'required|in:owner,branch_manager,admin_klinik,admin_produk',
        ];
    }
}
