<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

test('admin can delete a product', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    Storage::fake('public');

    $product = Product::factory()->create([
        'image_path' => UploadedFile::fake()->image('photo.png')->store('products', 'public')
    ]);

    $response = deleteJson("/api/products/{$product->id}");

    $response->assertNoContent();
    assertDatabaseMissing('products', [
        'id' => $product->id
    ]);
    Storage::disk('public')->assertMissing($product->image_path);
});

test('regular user cannot delete a product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $response = deleteJson("/api/products/{$product->id}");

    $response->assertForbidden();
});

test('guest cannot delete a product', function () {

    $response = deleteJson("/api/products/1");

    $response->assertUnauthorized();
});
