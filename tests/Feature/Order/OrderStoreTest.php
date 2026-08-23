<?php

use App\Models\Address;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);


test('authenticated user can checkout successfully without coupon', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $user->id,
    ]);

    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'price' => 100,
        'stock' => 10,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = postJson('/api/orders', [
        'address_id' => $address->id,
    ]);

    $response->assertCreated();
    assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'final_price' => 200,
        'discount_amount' => 0,
        'status' => 'pending',
    ]);
    assertDatabaseHas('order_items', [
        'order_id' => $response->json('data.id'),
        'product_id' => $product->id,
        'quantity' => 2,
        'item_name' => $product->name,
        'item_description' => $product->description,
        'item_price' => 100,
    ]);
    assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 8,
    ]);
    assertDatabaseMissing('cart_items', [
        'cart_id' => $cart->id,
    ]);
});

test('authenticated user can checkout successfully with a valid coupon', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $user->id,
    ]);

    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'price' => 100,
        'stock' => 10,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $coupon = Coupon::factory()->create([
        'discount_percentage' => 10,
        'is_active' => true,
        'expires_at' => now()->addDay(),
    ]);

    $response = postJson('/api/orders', [
        'address_id' => $address->id,
        'coupon_id' => $coupon->id
    ]);

    $response->assertCreated();
    assertDatabaseHas('orders', [
        'id' => $response->json('data.id'),
        'user_id' => $user->id,
        'final_price' => 180,
        'discount_amount' => 20,
        'coupon_id' => $coupon->id,
        'coupon_code' => $coupon->code,
        'status' => 'pending',
    ]);
    assertDatabaseHas('order_items', [
        'order_id' => $response->json('data.id'),
        'product_id' => $product->id,
        'quantity' => 2,
        'item_price' => 100,
    ]);
    assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 8,
    ]);
    $this->assertDatabaseMissing('cart_items', [
        'cart_id' => $cart->id,
    ]);
});

test('cannot checkout with an empty cart', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $user->id,
    ]);

    Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = postJson('/api/orders', [
        'address_id' => $address->id,
    ]);
    $response->assertServerError();
});

test('cannot checkout when stock is insufficient', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $user->id,
    ]);

    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'price' => 100,
        'stock' => 1,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = postJson('/api/orders', [
        'address_id' => $address->id,
    ]);

    $response->assertServerError();
    assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 1,
    ]);
});


test('cannot checkout with an invalid coupon', function (array $couponData) {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $user->id,
    ]);

    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'price' => 100,
        'stock' => 10,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $coupon = Coupon::factory()->create($couponData);


    $response = postJson('/api/orders', [
        'address_id' => $address->id,
        'coupon_id' => $coupon->id,
    ]);

    $response->assertServerError();
})->with([
    'inactive coupon' => [
        [
            'is_active' => false,
            'expires_at' => now()->addDay(),
        ],
    ],

    'expired coupon' => [
        [
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ],
    ],
]);


test('cannot checkout using another user address', function () {
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();

    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $anotherUser->id,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'price' => 100,
        'stock' => 10,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $response = postJson('/api/orders', [
        'address_id' => $address->id,
    ]);

    $response->assertServerError();
    assertDatabaseMissing('orders', [
        'user_id' => $user->id,
    ]);
});

test('guest cannot create an order', function () {
    $response = $this->postJson('/api/orders', []);

    $response->assertUnauthorized();
});
