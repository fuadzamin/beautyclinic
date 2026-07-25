<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id'        => 'required|exists:branches,id',
            'treatment_id'     => 'required|exists:treatments,id',
            'appointment_date' => 'required|date|after:now|before:' . now()->addDays(30)->toDateTimeString(),
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/'],
            'customer_concern' => 'nullable|string|max:500',
            'staff_id'         => 'nullable|exists:staff,id',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex'    => 'Phone number must use Indonesian format (e.g., 628123456789 or 08123456789).',
            'appointment_date.before' => 'Appointments can only be booked up to 30 days in advance.',
        ];
    }
}
