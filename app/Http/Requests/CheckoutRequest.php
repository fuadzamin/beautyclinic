<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth handled by middleware
    }

    public function rules(): array
    {
        return [
            'branch_id'        => ['required', 'exists:branches,id'],
            'appointment_id'   => ['nullable', 'exists:appointments,id', function ($attribute, $value, $fail) {
                if ($value && \App\Models\Transaction::where('appointment_id', $value)->exists()) {
                    $fail('Appointment ini sudah memiliki transaksi dan tidak dapat diproses ulang.');
                }
            }],
            'customer_name'    => ['required', 'string', 'max:100'],
            'customer_phone'   => ['nullable', 'string', 'max:20'],
            'payment_method'   => ['required', 'in:cash,transfer,qris,card'],
            'amount_paid'      => ['required', 'integer', 'min:0'],
            'discount'         => ['nullable', 'integer', 'min:0'],
            'points_redeemed'  => ['nullable', 'integer', 'min:0'],
            'notes'            => ['nullable', 'string'],
            'items'            => ['nullable', 'array'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.item_name'  => ['required_with:items', 'string'],
            'items.*.quantity'   => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'integer', 'min:0'],
        ];
    }
}
