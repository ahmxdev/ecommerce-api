<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

test('admin order status through an allowed transition', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $order = Order::factory()->create([
        'user_id' => $admin->id,
        'status' => 'pending',
    ]);

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'preparing',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.id', $order->id)
        ->assertJsonPath('data.status', 'preparing');

    expect($order->fresh()->status)->toBe('preparing');
});

test('regular user cannot update an order status', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $order = Order::factory()->create();

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'preparing',
    ]);

    $response->assertForbidden();

    expect($order->fresh()->status)->toBe('pending');
});

test('cannot update order to an invalid status transition', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $order = Order::factory()->create([
        'user_id' => $admin->id,
        'status' => 'pending',
    ]);

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'delivered',
    ]);

    $response->assertUnprocessable();

    expect($order->fresh()->status)->toBe('pending');
});

test('guest cannot update order status', function () {
    $order = Order::factory()->create([
        'status' => 'pending',
    ]);

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'preparing',
    ]);

    $response->assertUnauthorized();
});

test('status must be a valid order status', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $order = Order::factory()->create([
        'user_id' => $admin->id,
        'status' => 'pending',
    ]);

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'invalid-status',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);

    expect($order->fresh()->status)->toBe('pending');
});
