<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateProductStockRequest;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    // GET /api/v1/products
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->list($request->all());

        return $this->success($products, 'Products retrieved');
    }

    // GET /api/v1/products/:id
    public function show(Product $product): JsonResponse
    {
        $product->increment('views');

        return $this->success($product->load('branches'), 'Product retrieved');
    }

    // POST /api/v1/products (admin only)
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = url('storage/' . $path);
        }

        $product = Product::create($data);

        return $this->success($product, 'Product created', 201);
    }

    // PUT /api/v1/products/:id (admin only)
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = url('storage/' . $path);
        }

        $product->update($data);

        return $this->success($product->fresh(), 'Product updated');
    }

    // DELETE /api/v1/products/:id (admin only — soft delete)
    public function destroy(Product $product): JsonResponse
    {
        $product->update(['is_active' => false]);
        $product->delete();

        return $this->success(null, 'Product deactivated', 204);
    }

    // PUT /api/v1/products/:id/stock (admin only)
    public function updateStock(UpdateProductStockRequest $request, Product $product): JsonResponse
    {
        $user = auth()->guard('api_staff')->user();
        $branchId = $request->integer('branch_id');

        // RBAC check
        if ($user && in_array($user->role, ['branch_manager', 'admin_produk'])) {
            if ($user->branch_id !== $branchId) {
                return $this->error('Unauthorized for this branch', 403);
            }
        }

        $updatedProduct = $this->productService->updateStock(
            $product,
            $branchId,
            $request->integer('stock_quantity')
        );

        return $this->success($updatedProduct, 'Stock updated');
    }
}
