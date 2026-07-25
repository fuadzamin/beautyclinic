<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Update product stock for a specific branch
     */
    public function updateStock(Product $product, int $branchId, int $quantity): Product
    {
        $product->branches()->syncWithoutDetaching([
            $branchId => ['stock_quantity' => $quantity]
        ]);

        return $product->load('branches');
    }

    /**
     * Get products with filtering
     */
    public function list(array $filters)
    {
        return Product::with(['branches'])
            ->when(!($filters['show_all'] ?? false), fn ($q) => $q->active())
            ->when($filters['category'] ?? null, fn ($q) => $q->byCategory($filters['category']))
            ->when($filters['min_price'] ?? null, fn ($q) => $q->where('price', '>=', (int)$filters['min_price']))
            ->when($filters['max_price'] ?? null, fn ($q) => $q->where('price', '<=', (int)$filters['max_price']))
            ->when($filters['search'] ?? null, fn ($q) => $q->where('name', 'like', "%{$filters['search']}%"))
            ->when($filters['branch_id'] ?? null, function ($q) use ($filters) {
                $q->whereHas('branches', fn ($q2) => $q2->where('branches.id', $filters['branch_id']));
            })
            ->orderByDesc('views')
            ->paginate($filters['per_page'] ?? 15);
    }
}
