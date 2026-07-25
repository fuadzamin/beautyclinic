<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptSetting extends Model
{
    protected $fillable = [
        'branch_id',
        'clinic_name',
        'tagline',
        'address',
        'phone',
        'email',
        'logo_url',
        'show_treatment',
        'show_products',
        'show_discount',
        'show_payment_method',
        'show_cashier_name',
        'show_appointment_date',
        'footer_message',
        'social_instagram',
        'social_whatsapp',
        'website',
        'auto_print',
    ];

    protected function casts(): array
    {
        return [
            'show_treatment'        => 'boolean',
            'show_products'         => 'boolean',
            'show_discount'         => 'boolean',
            'show_payment_method'   => 'boolean',
            'show_cashier_name'     => 'boolean',
            'show_appointment_date' => 'boolean',
            'auto_print'            => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get setting for a specific branch, falling back to global setting or defaults.
     */
    public static function forBranch(?int $branchId): self
    {
        // Try branch-specific setting first, then global (branch_id = null)
        return static::where('branch_id', $branchId)->first()
            ?? static::whereNull('branch_id')->first()
            ?? new static(); // Return default values if nothing configured
    }
}
