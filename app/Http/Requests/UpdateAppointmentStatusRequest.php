<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles role check
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ];
    }
}
