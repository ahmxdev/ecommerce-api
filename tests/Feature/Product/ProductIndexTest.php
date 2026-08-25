<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('authinticated user can list products', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

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

test('guest cannot list products', function () {
    $response = getJson('/api/products');

    $response->assertUnauthorized();
});

test('empty product list', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson('/api/products');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});
