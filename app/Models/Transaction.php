<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'branch_id',
        'appointment_id',
        'staff_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'products_total',
        'discount',
        'grand_total',
        'payment_method',
        'payment_status',
        'amount_paid',
        'change_amount',
        'points_redeemed',
        'points_earned',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'        => 'integer',
            'products_total'  => 'integer',
            'discount'        => 'integer',
            'grand_total'     => 'integer',
            'amount_paid'     => 'integer',
            'change_amount'   => 'integer',
            'points_redeemed' => 'integer',
            'points_earned'   => 'integer',
        ];
    }

    // Auto-generate transaction number before creating
    protected static function booted(): void
    {
        static::creating(function (Transaction $t) {
            $date   = now()->format('Ymd');
            $count  = static::whereDate('created_at', today())->count() + 1;
            $t->transaction_number = 'TRX-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
