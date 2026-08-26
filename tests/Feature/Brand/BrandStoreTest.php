<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('admin can create a brand', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [
        'name' => 'ASUS',
    ];

    $response = postJson('/api/brands', $data);

    $response->assertCreated();
    assertDatabaseHas('brands', [
        'name' => $data['name']
    ]);
    $response->assertJsonPath('data.name', $data['name']);
});

test('regular user cannot create a brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = postJson('/api/brands', [
        'name' => 'ASUS',
    ]);

    $response->assertForbidden();
});

test('guest cannot create a brand', function () {
    $data = [
        'name' => 'ASUS',
    ];

    $response = postJson('/api/brands', $data);

    $response->assertUnauthorized();
});

test('validation fails when name is missing', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [];

    $response = postJson('/api/brands', $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name']);
});

test('validation fails when name already exists', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [
        'name' => 'ASUS',
    ];

    Brand::factory()->create([
        'name' => $data['name']
    ]);

    $response = postJson('/api/brands', $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name']);
});
