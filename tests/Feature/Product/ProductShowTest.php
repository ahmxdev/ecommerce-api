<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('authinticated user can show a product and load its relations', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $category = Category::factory()->create();
    // IMAGES
    $product = Product::factory()->create();
    $product->categories()->sync($category->id);

    $response = getJson("/api/admin/products/{$product->id}");

    $response->assertOk();
    $response->assertJsonPath('data.id', $product->id);
    $response->assertJsonStructure([
        'data' => [
            'id',
            'name',
            'description',
            'price',
            'stock',
            'brand',
            'categories',
            'images'
        ]
    ]);
});

test('guest cannot show a product', function () {
    $response = getJson("/api/admin/products/1");

    $response->assertUnauthorized();
});

test('showing a non-existing product returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson("/api/admin/products/99999");

    $response->assertNotFound();
});
