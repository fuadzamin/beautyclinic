<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppService
{
    public function generateOrderLink(Order $order): string
    {
        $items = $order->items->map(
            fn ($item) => "- {$item->product->name} x{$item->quantity} = Rp " . number_format($item->price_at_purchase * $item->quantity)
        )->join("\n");

        $message  = "Halo! Pesanan #{$order->order_number}\n\n";
        $message .= "{$items}\n\n";
        $message .= "Total: Rp " . number_format($order->total_price) . "\n";
        $message .= "Pembayaran: Cash on Delivery\n\n";
        $message .= "Silakan konfirmasi pesanan Anda. Terima kasih! 🌸";

        $phone = ltrim($order->customer_phone, '0');
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    public function generateAppointmentLink(string $phone, string $customerName, string $datetime): string
    {
        $message  = "Halo {$customerName}! 🌸\n\n";
        $message .= "Konfirmasi janji temu Anda:\n";
        $message .= "📅 {$datetime}\n\n";
        $message .= "Harap hadir 5 menit sebelum waktu yang ditentukan.\n";
        $message .= "Untuk reschedule, silakan hubungi kami.";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}
