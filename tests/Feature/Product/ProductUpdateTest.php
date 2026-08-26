<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);


test('admin can update a product', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    Storage::fake('public');

    $brand1 = Brand::factory()->create();
    $brand2 = Brand::factory()->create();
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();

    $product = Product::create([
        'name' => 'M',
        'description' => '',
        'image_path' => '/dir/photo.png',
        'slug' => 'm',
        'price' => 10,
        'stock' => 5,
        'brand_id' => $brand1->id,
    ]);
    $product->categories()->sync($category1->id);

    $data = [
        'name' => 'Mouse name',
        'description' => '',
        'image' => UploadedFile::fake()->image('mouse.jpg'),
        'slug' => 'mouse-name',
        'price' => 100,
        'stock' => 50,
        'brand_id' => $brand2->id,
        'categories' => [$category2->id],
    ];

    $response = putJson("/api/products/{$product->id}", $data);

    $response->assertOk();
    assertDatabaseHas('products', [
        'name' => $data['name']
    ]);
    $product = Product::first();
    assertDatabaseHas('product_category', [
        'product_id' => $product->id,
        'category_id' => $data['categories'][0],
    ]);
    $response->assertJsonPath('data.name', $data['name']);
    $response->assertJsonFragment([
        'name' => $data['name'],
        'image_url' => Storage::url($product->image_path)
    ]);
});

test('regular user cannot update a product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $response = putJson("/api/products/{$product->id}", []);

    $response->assertForbidden();
});

test('guest cannot update a product', function () {
    $response = putJson("/api/products/1", []);

    $response->assertUnauthorized();
});
