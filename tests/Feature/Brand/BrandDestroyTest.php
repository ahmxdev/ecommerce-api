<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

test('admin can delete a brand', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $brand = Brand::factory()->create();

    $response = deleteJson("/api/brands/{$brand->id}");

    $response->assertNoContent();
    assertDatabaseMissing('brands', [
        'id' => $brand->id
    ]);
});

test('guest cannot delete a brand', function () {
    $brand = Brand::factory()->create();

    $response = deleteJson("/api/brands/{$brand->id}");

    $response->assertUnauthorized();
});

test('regular user cannot delete a brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $brand = Brand::factory()->create();

    $response = deleteJson("/api/brands/{$brand->id}");

    $response->assertForbidden();
});

test('admin gets 404 when deleting a non existing brand', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $response = deleteJson("/api/brands/99999");

    $response->assertNotFound();
});
