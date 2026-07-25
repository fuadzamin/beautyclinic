<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get sales report data
     */
    public function getSalesReport(array $filters): array
    {
        $query = Transaction::query()
            ->when($filters['branch_id'] ?? null, fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when($filters['start_date'] ?? null, fn($q) => $q->whereDate('created_at', '>=', $filters['start_date']))
            ->when($filters['end_date'] ?? null, fn($q) => $q->whereDate('created_at', '<=', $filters['end_date']));

        // Daily revenue for chart
        $dailyRevenue = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $query->sum('grand_total');
        $totalTransactions = $query->count();

        return [
            'total_revenue'      => (int) $totalRevenue,
            'total_transactions' => $totalTransactions,
            'daily_revenue'      => $dailyRevenue,
        ];
    }

    /**
     * Get top products and treatments
     */
    public function getTopPerforming(array $filters): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        // Top Products
        $topProducts = TransactionItem::whereNotNull('product_id')
            ->whereHas('transaction', function($q) use ($branchId, $startDate, $endDate) {
                $q->when($branchId, fn($q2) => $q2->where('branch_id', $branchId))
                  ->when($startDate, fn($q2) => $q2->whereDate('created_at', '>=', $startDate))
                  ->when($endDate, fn($q2) => $q2->whereDate('created_at', '<=', $endDate));
            })
            ->select('product_id', 'item_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_price) as total_revenue'))
            ->groupBy('product_id', 'item_name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Top Treatments (via appointments linked to transactions)
        $topTreatments = Transaction::whereNotNull('transactions.appointment_id')
            ->when($branchId, fn($q) => $q->where('transactions.branch_id', $branchId))
            ->when($startDate, fn($q) => $q->whereDate('transactions.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('transactions.created_at', '<=', $endDate))
            ->join('appointments', 'transactions.appointment_id', '=', 'appointments.id')
            ->join('treatments', 'appointments.treatment_id', '=', 'treatments.id')
            ->select('treatments.name', DB::raw('COUNT(*) as total_bookings'), DB::raw('SUM(transactions.subtotal) as total_revenue'))
            ->groupBy('treatments.name')
            ->orderByDesc('total_bookings')
            ->limit(10)
            ->get();

        return [
            'top_products'   => $topProducts,
            'top_treatments' => $topTreatments,
        ];
    }

    /**
     * Get customer demographics
     */
    public function getDemographics(array $filters): array
    {
        // Gender Distribution
        $genderDist = DB::table('users')
            ->select('gender', DB::raw('count(*) as count'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get();

        // Age Group Distribution
        $ageDist = DB::table('users')
            ->select(DB::raw("
                CASE 
                    WHEN birthdate IS NULL THEN 'Unknown'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 20 THEN '< 20'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 20 AND 30 THEN '20-30'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 31 AND 45 THEN '31-45'
                    ELSE '> 45'
                END as age_group
            "), DB::raw('count(*) as count'))
            ->groupBy('age_group')
            ->get();

        return [
            'gender_distribution' => $genderDist,
            'age_distribution'    => $ageDist,
        ];
    }
}
