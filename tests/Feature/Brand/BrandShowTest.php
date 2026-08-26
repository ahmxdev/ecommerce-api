<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('admin can view a brand', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $brand = Brand::factory()->create();

    $response = getJson("/api/brands/{$brand->id}");

    $response->assertOk();
    $response->assertJsonPath('data.id', $brand->id);
    $response->assertJsonPath('data.name', $brand->name);
});

test('admin gets 404 when the brand does not exist.', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $response = getJson('/api/brands/999999');

    $response->assertNotFound();
});


test('regular user cannot view a brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $brand = Brand::factory()->create();

    $response = getJson("/api/brands/{$brand->id}");

    $response->assertForbidden();
});

test('guest cannot view a brand', function () {
    $response = getJson('/api/brands');

    $response->assertUnauthorized();
});
