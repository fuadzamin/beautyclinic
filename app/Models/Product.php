<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image_url',
        'ingredients',
        'volume',
        'views',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'          => 'integer',
            'views'          => 'integer',
            'is_active'      => 'boolean',
        ];
    }

    // Relationships
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_product')
                    ->withPivot('stock_quantity')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->whereHas('branches', function ($q) use ($threshold) {
            $q->where('stock_quantity', '<', $threshold);
        });
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
