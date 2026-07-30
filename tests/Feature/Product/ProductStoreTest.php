<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);


test('authinticated user can create a product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $brand = Brand::factory()->create();
    $categories = Category::factory()->count(2)->create();
    $data = [
        'name' => 'Mouse name',
        'description' => '',
        'slug' => 'mouse-name',
        'price' => 100,
        'stock' => 50,
        'brand_id' => $brand->id,
        'categories' => $categories->pluck('id')->all(),
    ];

    $response = postJson("/api/admin/products", $data);

    $response->assertCreated();
    assertDatabaseHas('products', [
        'name' => $data['name']
    ]);
    assertDatabaseHas('product_category', [
        'category_id' => $data['categories'][0],
        'product_id' => $response->json('data.id')
    ]);
    $response->assertJsonPath('data.name', $data['name']);
});

test('guest cannot create a product', function () {
    $response = postJson("/api/admin/products", []);

    $response->assertUnauthorized();
});
