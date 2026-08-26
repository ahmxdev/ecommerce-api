<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

test('authenticated user can update cart item quantity', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        'stock' => 10,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = putJson("/api/cart/items/{$cartItem->id}", [
        'quantity' => 5,
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'message' => 'item updated',
        ]);

    assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 5,
    ]);
});

test('guest cannot update cart item', function () {
    $cartItem = CartItem::factory()->create();

    $response = $this->putJson("/api/cart/items/{$cartItem->id}", [
        'quantity' => 2,
    ]);

    $response->assertUnauthorized();
});

test('user cannot update another user cart item', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $otherUser = User::factory()->create();

    $cart = Cart::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
    ]);

    $response = $this->putJson("/api/cart/items/{$cartItem->id}", [
        'quantity' => 5,
    ]);

    $response->assertNotFound();
});

test('user cannot update cart item with quantity greater than stock', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        'stock' => 5,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->putJson("/api/cart/items/{$cartItem->id}", [
        'quantity' => 6,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors('quantity');
});
