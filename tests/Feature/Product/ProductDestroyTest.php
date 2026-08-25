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

test('authenticated user can delete a product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
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

test('guest cannot delete a category', function () {

    $response = deleteJson("/api/products/1");

    $response->assertUnauthorized();
});
