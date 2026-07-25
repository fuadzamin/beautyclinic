<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_date' => ['sometimes', 'date', 'after:now'],
            'notes'            => ['sometimes', 'nullable', 'string', 'max:500'],
            'customer_concern' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
