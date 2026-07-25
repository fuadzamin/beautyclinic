<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetAvailableSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id'    => ['required', 'exists:branches,id'],
            'treatment_id' => ['required', 'exists:treatments,id'],
            'date'         => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}
