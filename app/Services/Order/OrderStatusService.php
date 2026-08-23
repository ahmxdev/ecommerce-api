<?php

namespace App\Services\Order;

use App\Models\Order;

class OrderStatusService
{
    public function changeStatus(Order $order, string $newStatus): Order
    {
        $currentStatus = $order->status;

        $allowedTransitions = [
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['delivered', 'cancelled'],
            'delivered' => [],
            'cancelled' => [],
        ];

        if (!in_array($newStatus, $allowedTransitions[$currentStatus], true)) {
            throw new \Exception('Invalid order status transition.');
        }

        $order->update([
            'status' => $newStatus,
        ]);

        return $order;
    }
}
