<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('authenticated user can view a brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $brand = Brand::factory()->create();

    $response = getJson("/api/brands/{$brand->id}");

    $response->assertOk();
    $response->assertJsonPath('data.id', $brand->id);
    $response->assertJsonPath('data.name', $brand->name);
});

test('authenticated user gets 404 when the brand does not exist.', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson("/api/brands/999999");

    $response->assertNotFound();
});

test('guest cannot view a brand', function () {
    $response = getJson('/api/brands');

    $response->assertUnauthorized();
});
