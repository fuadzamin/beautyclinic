<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;

class DashboardService
{
    public function getStats(string $role, ?int $branchId = null): array
    {
        return match ($role) {
            'owner' => $this->ownerStats(),
            'branch_manager' => $this->branchManagerStats($branchId),
            'admin_klinik' => $this->adminKlinikStats($branchId),
            'admin_produk' => $this->adminProdukStats($branchId),
            default => [],
        };
    }

    private function ownerStats(): array
    {
        return [
            'total_appointments'   => Appointment::count(),
            'total_orders'         => Order::count(),
            'pending_appointments' => Appointment::pending()->count(),
            'pending_orders'       => Order::pending()->count(),
            'total_revenue'        => (int) Transaction::sum('grand_total'),
            'active_customers'     => \App\Models\User::count(),
        ];
    }

    private function branchManagerStats(?int $branchId): array
    {
        $qAppt  = Appointment::when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $qOrder = Order::when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $qTrans = Transaction::when($branchId, fn($q) => $q->where('branch_id', $branchId));

        return [
            'total_appointments'   => (clone $qAppt)->count(),
            'pending_appointments' => (clone $qAppt)->pending()->count(),
            'total_orders'         => (clone $qOrder)->count(),
            'pending_orders'       => (clone $qOrder)->pending()->count(),
            'branch_revenue'       => (int) $qTrans->sum('grand_total'),
        ];
    }

    private function adminKlinikStats(?int $branchId): array
    {
        $qAppt = Appointment::when($branchId, fn($q) => $q->where('branch_id', $branchId));

        return [
            'today_appointments'    => (clone $qAppt)->today()->count(),
            'pending_confirmations' => (clone $qAppt)->pending()->count(),
            'no_show_count'         => (clone $qAppt)->where('status', 'no_show')->count(),
        ];
    }

    private function adminProdukStats(?int $branchId): array
    {
        return [
            'total_products'  => Product::active()->count(),
            'low_stock_count' => $branchId
                ? Product::whereHas('branches', fn($q) => $q->where('branches.id', $branchId)->where('stock_quantity', '<', 5))->count()
                : Product::lowStock()->count(),
            'pending_orders'  => Order::pending()->when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
        ];
    }
}
