<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('admin can show a product and load its relations', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $category = Category::factory()->create();
    $product = Product::factory()->create();
    $product->categories()->sync($category->id);

    $response = getJson("/api/products/{$product->id}");

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
            'image_url'
        ]
    ]);
});

test('regular user cannot show a product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $response = getJson("/api/products/{$product->id}");

    $response->assertForbidden();
});

test('guest cannot show a product', function () {
    $response = getJson("/api/products/1");

    $response->assertUnauthorized();
});

test('showing a non-existing product returns 404', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $response = getJson("/api/products/99999");

    $response->assertNotFound();
});
