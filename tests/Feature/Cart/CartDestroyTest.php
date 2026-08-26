<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);


test('authenticated user can delete his cart item', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
    ]);

    $response = deleteJson("/api/cart/items/{$cartItem->id}");

    $response->assertNoContent();

    assertDatabaseMissing('cart_items', [
        'id' => $cartItem->id,
    ]);
});

test('guest cannot delete cart item', function () {
    $cartItem = CartItem::factory()->create();

    $response = deleteJson("/api/cart/items/{$cartItem->id}");

    $response->assertUnauthorized();
});

test('user cannot delete another user cart item', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $otherUser = User::factory()->create();

    $cart = Cart::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
    ]);

    $response = deleteJson("/api/cart/items/{$cartItem->id}");

    $response->assertNotFound();
    assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
    ]);
});

test('returns not found when cart item does not exist', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson('/api/cart/items/99999');

    $response->assertNotFound();
});
