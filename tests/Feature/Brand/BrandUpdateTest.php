<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

test('authenticated user can update a brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'name' => 'ASUS',
    ];

    $brand = Brand::factory()->create([
        'name' => 'DELL'
    ]);

    $response = putJson("/api/brands/{$brand->id}", $data);

    $response->assertOk();
    assertDatabaseHas('brands', [
        'name' => $data['name']
    ]);
    $response->assertJsonPath('data.name', $data['name']);
});

test('authenticated user can keep the same brand name', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'name' => 'ASUS',
    ];

    $brand = Brand::factory()->create([
        'name' => 'ASUS'
    ]);

    $response = putJson("/api/brands/{$brand->id}", $data);

    $response->assertOk();
    assertDatabaseHas('brands', [
        'id' => $brand->id,
        'name' => $data['name'],
    ]);
    $response->assertJsonPath('data.name', $data['name']);
});

test('validation fails when updating to an existing brand name', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'name' => 'ASUS',
    ];

    $existingBrand = Brand::factory()->create([
        'name' => 'ASUS'
    ]);
    $brand = Brand::factory()->create([
        'name' => 'DELL'
    ]);

    $response = putJson("/api/brands/{$brand->id}", $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        'name',
    ]);
});

test('validation fails when name is missing', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $brand = Brand::factory()->create();

    $response = putJson("/api/brands/{$brand->id}", []);

    $response->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'name',
    ]);
});

test('guest cannot update a brand', function () {
    $brand = Brand::factory()->create();
    $data = [
        'name' => 'ASUS'
    ];

    $response = putJson("/api/brands/{$brand->id}", $data);

    $response->assertUnauthorized();
});
