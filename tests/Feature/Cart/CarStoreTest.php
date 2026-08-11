<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);


test('authenticated user can add product to cart', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        'stock' => 10,
    ]);

    $data = [
        'product_id' => $product->id,
        'quantity' => 2,
    ];

    $response = postJson('/api/cart/items', $data);

    $response->assertCreated();

    assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});


test('guest cannot add product to cart', function () {
    $product = Product::factory()->create();

    $response = $this->postJson('/api/cart/items', [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $response->assertUnauthorized();
});
