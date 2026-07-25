<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/'],
            'phone'    => 'nullable|string|max:20|unique:users,phone',
            'address'  => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must contain at least one letter and one number.',
        ];
    }
}
