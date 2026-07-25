<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\CheckoutRequest;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    /**
     * GET /api/v1/admin/pos/appointments
     * Appointments for today that are ready to be billed (status = completed, not yet transacted)
     */
    public function todayAppointments(Request $request): JsonResponse
    {
        $user = auth()->user();
        $branchId = $request->query('branch_id');
        $apptId   = $request->query('appointment_id');

        // Enforce branch manager restriction
        if ($user && $user->role === 'branch_manager') {
            $branchId = $user->branch_id;
        }

        $data = $this->transactionService->getPendingAppointments($branchId, $apptId);

        return $this->success($data, $apptId ? ($data ? 'Appointment retrieved' : 'Appointment not found or already paid') : 'Completed appointments retrieved');
    }

    /**
     * GET /api/v1/admin/pos/transactions
     * List all transactions (with filter)
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = $this->transactionService->listTransactions($request->all());

        return $this->success($transactions, 'Transactions retrieved');
    }

    /**
     * POST /api/v1/admin/pos/checkout
     * Process a POS transaction / payment
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->checkout($request->validated());

            return $this->success($transaction, 'Transaction processed successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }


    /**
     * GET /api/v1/admin/pos/transactions/summary
     * Server-side revenue summary (accurate across all data, not just current page)
     */
    public function summary(Request $request): JsonResponse
    {
        $summary = $this->transactionService->getSummary($request->all());

        return $this->success($summary);
    }

    /**
     * GET /api/v1/admin/pos/transactions/:id
     */
    public function show(Transaction $transaction): JsonResponse
    {
        return $this->success($transaction->load(['items.product', 'appointment.treatment', 'branch', 'staff']), 'Transaction retrieved');
    }
}
