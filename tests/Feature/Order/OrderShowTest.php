<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('authenticated user can view their order', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $order = Order::factory()->create([
        'user_id' => $user->id,
    ]);

    $product = Product::factory()->create();

    $item = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
    ]);

    $response = getJson("/api/orders/{$order->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $order->id)
        ->assertJsonPath('data.items.0.id', $item->id)
        ->assertJsonPath('data.items.0.product_id', $product->id);
});



test('user cannot view another user order', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Sanctum::actingAs($user);

    $order = Order::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = getJson("/api/orders/{$order->id}");

    $response->assertNotFound();
});


test('guest cannot view an order', function () {
    $order = Order::factory()->create();

    $response = getJson("/api/orders/{$order->id}");

    $response->assertUnauthorized();
});
