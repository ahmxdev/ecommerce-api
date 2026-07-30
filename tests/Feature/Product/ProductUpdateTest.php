<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);


test('authinticated user can update a product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $brand1 = Brand::factory()->create();
    $brand2 = Brand::factory()->create();
    $categories = Category::factory()->count(2)->create();
    $product = Product::create([
        'name' => 'M',
        'description' => '',
        'slug' => 'm',
        'price' => 10,
        'stock' => 5,
        'brand_id' => $brand1->id,
    ]);
    $product->categories()->sync($categories[0]->id);

    $data = [
        'name' => 'Mouse name',
        'description' => '',
        'slug' => 'mouse-name',
        'price' => 100,
        'stock' => 50,
        'brand_id' => $brand2->id,
        'categories' => [$categories[1]->id],
    ];

    $response = putJson("/api/admin/products/{$product->id}", $data);

    $response->assertOk();
    assertDatabaseHas('products', [
        'name' => $data['name']
    ]);
    assertDatabaseHas('product_category', [
        'category_id' => $data['categories'][0],
        'product_id' => $product->id
    ]);
    $response->assertJsonPath('data.name', $data['name']);
});

test('guest cannot update a product', function () {
    $response = putJson("/api/admin/products/1", []);

    $response->assertUnauthorized();
});
