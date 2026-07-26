<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

test('authenticated user can delete a brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $brand = Brand::factory()->create();

    $response = deleteJson("/api/admin/brands/{$brand->id}");

    $response->assertNoContent();
    assertDatabaseMissing('brands', [
        'id' => $brand->id
    ]);
});

test('guest cannot delete a brand', function () {
    $brand = Brand::factory()->create();

    $response = deleteJson("/api/admin/brands/{$brand->id}");

    $response->assertUnauthorized();
});

test('authenticated user gets 404 when deleting a non existing brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = deleteJson("/api/admin/brands/99999");

    $response->assertNotFound();
});
