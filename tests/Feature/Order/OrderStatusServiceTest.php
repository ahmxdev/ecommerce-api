<?php

use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can change order status through an allowed transition', function () {
    $user = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $service = new OrderStatusService();

    $service->changeStatus($order, 'preparing');

    expect($order->fresh()->status)->toBe('preparing');
});

test('cannot change order status through a forbidden transition', function () {
    $user = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $service = new OrderStatusService();

    expect(fn() => $service->changeStatus($order, 'delivered'))
        ->toThrow(\Exception::class, 'Invalid order status transition.');

    expect($order->fresh()->status)->toBe('pending');
});

test('cannot change status of a final order', function (string $status) {
    $user = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => $status,
    ]);

    $service = new OrderStatusService();

    expect(fn() => $service->changeStatus($order, 'pending'))
        ->toThrow(\Exception::class, 'Invalid order status transition.');

    expect($order->fresh()->status)->toBe($status);
})->with([
    'delivered',
    'cancelled',
]);
