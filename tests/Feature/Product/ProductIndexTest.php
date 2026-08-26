<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('admin can list products', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    Product::factory()->count(5)->create();

    $response = getJson('/api/products');

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'price',
                'stock',
                'image_url'
            ]
        ]
    ]);
    $product = Product::first();
    $response->assertJsonPath('data.0.image_url', Storage::url($product->image_path));
});

test('regular user cannot list products', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson('/api/products');

    $response->assertForbidden();
});

test('guest cannot list products', function () {
    $response = getJson('/api/products');

    $response->assertUnauthorized();
});

test('empty product list', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $response = getJson('/api/products');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});
