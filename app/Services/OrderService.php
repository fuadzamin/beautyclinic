<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $items      = $data['items'];
            $totalPrice = 0;
            $deliveryMethod = $data['delivery_method'];
            $fulfilledBranchId = null;
            $products   = [];

            if ($deliveryMethod === 'pickup') {
                $fulfilledBranchId = $data['branch_id'];
                // Check stock specifically at chosen branch
                foreach ($items as $item) {
                    $product = Product::active()->findOrFail($item['product_id']);
                    $branchStock = DB::table('branch_product')
                        ->where('branch_id', $fulfilledBranchId)
                        ->where('product_id', $product->id)
                        ->first();
                        
                    if (!$branchStock || $branchStock->stock_quantity < $item['quantity']) {
                        throw new \Exception("Stok tidak mencukupi untuk {$product->name} di cabang yang dipilih.");
                    }

                    $totalPrice += $product->price * $item['quantity'];
                    $products[] = ['product' => $product, 'quantity' => $item['quantity']];
                }
            } else {
                // Shipping: Find a branch that can fulfill all items
                $branches = Branch::all();
                foreach ($branches as $b) {
                    $canFulfill = true;
                    foreach ($items as $item) {
                        $stock = DB::table('branch_product')
                            ->where('branch_id', $b->id)
                            ->where('product_id', $item['product_id'])
                            ->value('stock_quantity') ?? 0;
                            
                        if ($stock < $item['quantity']) {
                            $canFulfill = false;
                            break;
                        }
                    }
                    if ($canFulfill) {
                        $fulfilledBranchId = $b->id;
                        break;
                    }
                }

                if (!$fulfilledBranchId) {
                    throw new \Exception("Maaf, tidak ada satupun cabang yang memiliki stok gabungan yang cukup untuk pesanan Anda. Silakan coba kurangi jumlah barang.");
                }

                // Calculate price
                foreach ($items as $item) {
                    $product = Product::active()->findOrFail($item['product_id']);
                    $totalPrice += $product->price * $item['quantity'];
                    $products[] = ['product' => $product, 'quantity' => $item['quantity']];
                }
            }

            // Create order
            $order = Order::create([
                'user_id'                => Auth::guard('api')->id(),
                'branch_id'              => $deliveryMethod === 'pickup' ? $fulfilledBranchId : null,
                'total_price'            => $totalPrice,
                'customer_name'          => $data['customer_name'],
                'customer_phone'         => $data['customer_phone'],
                'notes'                  => $data['notes'] ?? null,
                'delivery_method'        => $deliveryMethod,
                'shipping_address'       => $deliveryMethod === 'shipping' ? $data['shipping_address'] : null,
                'fulfilled_by_branch_id' => $fulfilledBranchId,
            ]);

            // Create order items & deduct stock
            foreach ($products as $row) {
                $order->items()->create([
                    'product_id'       => $row['product']->id,
                    'quantity'         => $row['quantity'],
                    'price_at_purchase' => $row['product']->price,
                ]);

                // Deduct from branch_product pivot
                DB::table('branch_product')
                    ->where('branch_id', $fulfilledBranchId)
                    ->where('product_id', $row['product']->id)
                    ->decrement('stock_quantity', $row['quantity']);

                // Note: low stock alert logic needs update since we moved to pivot, 
                // skipping complex alert for now or alert specific branch.
            }

            // Award loyalty points (if logged in)
            if ($userId = Auth::guard('api')->id()) {
                $this->awardLoyaltyPoints($userId, $totalPrice, 'product_purchase', $order->id);
            }

            // Notify admin_produk
            Notification::create([
                'staff_id' => null,
                'title'    => "New Order #{$order->order_number}",
                'message'  => "New order from {$order->customer_name}. Total: Rp " . number_format($totalPrice),
                'type'     => 'order',
            ]);

            return $order;
        });
    }

    public function updateStatus(Order $order, string $status): void
    {
        $order->update(['status' => $status]);

        // Award loyalty points on completion
        if ($status === 'completed' && $order->user_id) {
            $this->awardLoyaltyPoints($order->user_id, $order->total_price, 'product_purchase', $order->id);
        }
    }

    private function awardLoyaltyPoints(int $userId, int $amount, string $source, int $sourceId): void
    {
        $points = (int) floor($amount / 10000); // 1 point per Rp 10,000

        if ($points <= 0) return;

        $user        = User::find($userId);
        $totalPoints = $user->loyalty_points + $points;

        LoyaltyPoint::create([
            'user_id'       => $userId,
            'points_earned' => $points,
            'total_points'  => $totalPoints,
            'source'        => $source,
            'source_id'     => $sourceId,
        ]);

        $user->increment('loyalty_points', $points);
    }
}
