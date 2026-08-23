<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

test('user can update their order status through an allowed transition', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $order = Order::factory()->create([
        'user_id' => $user->id,
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

test('user cannot update another user order status', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Sanctum::actingAs($user);

    $order = Order::factory()->create([
        'user_id' => $otherUser->id,
        'status' => 'pending',
    ]);

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'preparing',
    ]);

    $response->assertNotFound();

    expect($order->fresh()->status)->toBe('pending');
});

test('cannot update order to an invalid status transition', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'delivered',
    ]);

    $response->assertStatus(500);

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
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $response = patchJson("/api/orders/{$order->id}/status", [
        'status' => 'invalid-status',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);

    expect($order->fresh()->status)->toBe('pending');
});
