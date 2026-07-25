<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Product;
use App\Models\Transaction;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Process a POS checkout transaction
     */
    public function checkout(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $appointment = null;
            $subtotal    = 0;

            // 1. Handle Appointment Treatment
            if (!empty($data['appointment_id'])) {
                $appointment = Appointment::with(['treatment', 'user'])->findOrFail($data['appointment_id']);
                $subtotal    = $appointment->treatment?->price ?? 0;
            }

            // 2. Handle Product Items
            $productsTotal = 0;
            $itemsData     = [];
            foreach ($data['items'] ?? [] as $item) {
                $lineTotal       = $item['unit_price'] * $item['quantity'];
                $productsTotal  += $lineTotal;
                $itemsData[]     = [
                    'product_id'  => $item['product_id'] ?? null,
                    'item_name'   => $item['item_name'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'total_price' => $lineTotal,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            // 3. Totals Calculation
            $discount       = $data['discount'] ?? 0;
            $pointsRedeemed = $data['points_redeemed'] ?? 0;
            $pointsValue    = $pointsRedeemed * 1000;
            
            $grandTotal     = $subtotal + $productsTotal - $discount - $pointsValue;
            $grandTotal     = max(0, $grandTotal);
            
            $amountPaid     = $data['amount_paid'];
            $change         = max(0, $amountPaid - $grandTotal);

            // Calculate points to earn (1 point for every 10k)
            $pointsEarned = floor($grandTotal / 10000);

            // 4. Create Transaction Record
            $transaction = Transaction::create([
                'branch_id'       => $data['branch_id'],
                'appointment_id'  => $data['appointment_id'] ?? null,
                'staff_id'        => $appointment?->staff_id ?? auth('api_staff')->id(),
                'customer_name'   => $data['customer_name'],
                'customer_phone'  => $data['customer_phone'] ?? null,
                'subtotal'        => $subtotal,
                'products_total'  => $productsTotal,
                'discount'        => $discount,
                'points_redeemed' => $pointsRedeemed,
                'points_earned'   => $pointsEarned,
                'grand_total'     => $grandTotal,
                'payment_method'  => $data['payment_method'],
                'payment_status'  => 'paid',
                'amount_paid'     => $amountPaid,
                'change_amount'   => $change,
                'notes'           => $data['notes'] ?? null,
            ]);

            ActivityLogger::log('pos_checkout', 'Transaction', $transaction->id, [
                'grand_total' => $grandTotal,
                'customer'    => $data['customer_name']
            ]);

            // 5. Handle Loyalty Points for User
            if ($appointment?->user) {
                $user = $appointment->user;
                
                // Redeem points
                if ($pointsRedeemed > 0) {
                    if ($user->loyalty_points < $pointsRedeemed) {
                        throw new \Exception("Poin pelanggan tidak mencukupi.");
                    }
                    $user->decrement('loyalty_points', $pointsRedeemed);
                    
                    // Log redemption
                    $user->loyaltyPoints()->create([
                        'points_earned' => -$pointsRedeemed,
                        'total_points'  => $user->loyalty_points,
                        'source'        => 'transaction_redeem',
                        'source_id'     => $transaction->id,
                    ]);
                }

                // Earn points
                if ($pointsEarned > 0) {
                    $user->increment('loyalty_points', $pointsEarned);
                    
                    // Log earning
                    $user->loyaltyPoints()->create([
                        'points_earned' => $pointsEarned,
                        'total_points'  => $user->loyalty_points,
                        'source'        => 'transaction_earn',
                        'source_id'     => $transaction->id,
                    ]);
                }
            }

            // 6. Update appointment status
            if ($appointment) {
                $appointment->update(['status' => 'completed']);
            }

            // 7. Insert Line Items
            if (!empty($itemsData)) {
                $transaction->items()->insert(
                    array_map(fn ($i) => array_merge($i, ['transaction_id' => $transaction->id]), $itemsData)
                );
            }

            // 8. Deduct Stock
            $this->deductStock($data['items'] ?? [], $data['branch_id']);

            return $transaction->load(['items.product', 'appointment.treatment', 'branch']);
        });
    }

    public function deductStock(array $items, int $branchId): void
    {
        foreach ($items as $item) {
            if (empty($item['product_id'])) continue;

            $product = Product::findOrFail($item['product_id']);
            
            $product->branches()->updateExistingPivot($branchId, [
                'stock_quantity' => DB::raw("stock_quantity - {$item['quantity']}")
            ]);
        }
    }

    public function getPendingAppointments(?int $branchId = null, ?int $apptId = null)
    {
        if ($apptId) {
            return Appointment::with(['treatment', 'branch', 'staff', 'user'])
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereDoesntHave('transaction')
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->find($apptId);
        }

        return Appointment::with(['treatment', 'branch', 'staff', 'user'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDoesntHave('transaction')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('appointment_date')
            ->get();
    }

    public function listTransactions(array $filters)
    {
        return Transaction::with(['branch', 'appointment.treatment', 'items.product'])
            ->when($filters['branch_id'] ?? null, fn ($q) => $q->where('branch_id', $filters['branch_id']))
            ->when($filters['date'] ?? null, fn ($q) => $q->whereDate('created_at', $filters['date']))
            ->orderByDesc('created_at')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function getSummary(array $filters)
    {
        $query = Transaction::query()
            ->when($filters['branch_id'] ?? null, fn ($q) => $q->where('branch_id', $filters['branch_id']))
            ->when($filters['date'] ?? null, fn ($q) => $q->whereDate('created_at', $filters['date']));

        $total    = $query->count();
        $revenue  = $query->sum('grand_total');
        $average  = $total > 0 ? round($revenue / $total) : 0;

        $topMethod = (clone $query)
            ->select('payment_method', DB::raw('count(*) as cnt'))
            ->groupBy('payment_method')
            ->orderByDesc('cnt')
            ->first();

        return [
            'total_transactions' => $total,
            'total_revenue'      => (int) $revenue,
            'average_value'      => (int) $average,
            'top_payment_method' => $topMethod?->payment_method ?? '-',
        ];
    }
}
