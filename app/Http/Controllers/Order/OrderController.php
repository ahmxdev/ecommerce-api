<?php

namespace App\Http\Controllers\Order;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\Order\OrderIndexResource;
use App\Http\Resources\Order\OrderShowResource;
use App\Models\Order;
use App\Services\Order\CheckoutService;
use App\Services\Order\OrderStatusService;

class OrderController
{
    private CheckoutService $checkoutService;
    private OrderStatusService $orderStatusService;
    public function __construct(CheckoutService $checkoutService, OrderStatusService $orderStatusService)
    {
        $this->checkoutService = $checkoutService;
        $this->orderStatusService = $orderStatusService;
    }

    public function index()
    {
        $orders = auth()->user()->orders()->latest()->get();
        return OrderIndexResource::collection($orders);
    }
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = $this->checkoutService->checkout(
            $request->user(),
            $data['address_id'],
            $data['coupon_id'] ?? null
        );

        return response()->json([
            'data' => $order
        ], 201);
    }
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(404);
        }

        $order->load('items');

        return new OrderShowResource($order);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(404);
        } // TEMP

        $order = $this->orderStatusService->changeStatus(
            $order,
            $request->validated('status')
        );

        return new OrderShowResource($order);
    }
}
