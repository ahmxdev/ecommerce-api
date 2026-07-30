<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

test('authenticated user can delete a product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $product = Product::factory()->create();

    $response = deleteJson("/api/admin/products/{$product->id}");

    $response->assertNoContent();
    assertDatabaseMissing('products', [
        'id' => $product->id
    ]);
});

test('guest cannot delete a category', function () {

    $response = deleteJson("/api/admin/products/1");

    $response->assertUnauthorized();
});
