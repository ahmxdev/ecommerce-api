<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;


uses(RefreshDatabase::class);

test('authenticated user can view his cart', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $product = Product::factory()->create();

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->getJson('/api/cart');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'items' => [
                    '*' => [
                        'id',
                        'product' => [
                            'id',
                            'name',
                            'price',
                        ],
                        'quantity',
                        'subtotal',
                    ],
                ],
            ],
        ])
        ->assertJsonPath('data.id', $cart->id)
        ->assertJsonPath('data.items.0.product.id', $product->id)
        ->assertJsonPath('data.items.0.quantity', 2);
});

test('guest cannot view cart', function () {
    $response = $this->getJson('/api/cart');

    $response->assertUnauthorized();
});
