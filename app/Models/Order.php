<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'branch_id',
        'total_price',
        'status',
        'payment_status',
        'customer_name',
        'customer_phone',
        'notes',
        'delivery_method',
        'shipping_address',
        'fulfilled_by_branch_id',
        'order_date',
        'pickup_date',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'integer',
            'order_date'  => 'datetime',
            'pickup_date' => 'datetime',
        ];
    }

    // Auto-generate order number before creation
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            $order->order_date   = $order->order_date ?? now();
        });
    }

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'fulfilled_by_branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity', 'price_at_purchase')
                    ->withTimestamps();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
