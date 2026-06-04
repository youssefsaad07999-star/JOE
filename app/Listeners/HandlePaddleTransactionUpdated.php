<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Payment;
use Laravel\Paddle\Events\TransactionUpdated;

class HandlePaddleTransactionUpdated
{
    public function handle(TransactionUpdated $event): void
    {
        $data = $event->payload['data'] ?? [];
        $status = $data['status'] ?? null;

        // Only act on payment failures
        if ($status !== 'payment_failed') {
            return;
        }

        $orderId = $data['custom_data']['order_id'] ?? null;

        if (! $orderId) {
            return;
        }

        $order = Order::find($orderId);

        if (! $order) {
            return;
        }

        $order->update(['status' => 'cancelled']);

        Payment::where('order_id', $order->id)
            ->update(['status' => 'failed']);
    }
}
