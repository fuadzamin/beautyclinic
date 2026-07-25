<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly WhatsAppService $whatsAppService,
    ) {}

    // POST /api/v1/orders
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return $this->success($order, 'Pesanan berhasil dibuat. Terima kasih!', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    // GET /api/v1/orders/:id
    public function show(Order $order): JsonResponse
    {
        // Only allow user to see their own order
        if ($order->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success($order->load('items.product'), 'Order retrieved');
    }

    // GET /api/v1/user/orders
    public function myOrders(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->success($orders, 'Orders retrieved');
    }

    // ──────── Admin endpoints ────────

    // GET /api/v1/admin/orders
    public function adminIndex(Request $request): JsonResponse
    {
        $staff = Auth::guard('api_staff')->user();

        $orders = Order::with(['user', 'items.product', 'branch', 'fulfilledBy'])
            ->when($staff && $staff->branch_id, fn ($q) => $q->where('fulfilled_by_branch_id', $staff->branch_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->delivery_method, fn ($q) => $q->where('delivery_method', $request->delivery_method))
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success($orders, 'Orders retrieved');
    }

    // PUT /api/v1/admin/orders/:id/status
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->orderService->updateStatus($order, $request->status);

        return $this->success($order->fresh(), 'Status pesanan berhasil diperbarui.');
    }

    // POST /api/v1/orders/:id/send-whatsapp (admin)
    public function sendWhatsApp(Order $order): JsonResponse
    {
        $waLink = $this->whatsAppService->generateOrderLink($order);

        return $this->success(['whatsapp_url' => $waLink], 'WhatsApp link generated');
    }
}
