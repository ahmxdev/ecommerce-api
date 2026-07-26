<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('authenticated user can create a brand', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'name' => 'ASUS',
    ];

    $response = postJson('/api/admin/brands', $data);

    $response->assertCreated();
    assertDatabaseHas('brands', [
        'name' => $data['name']
    ]);
    $response->assertJsonPath('data.name', $data['name']);
});

test('guest cannot create a brand', function () {
    $data = [
        'name' => 'ASUS',
    ];

    $response = postJson('/api/admin/brands', $data);

    $response->assertUnauthorized();
});

test('validation fails when name is missing', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [];

    $response = postJson('/api/admin/brands', $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name']);
});

test('validation fails when name already exists', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'name' => 'ASUS',
    ];

    Brand::factory()->create([
        'name' => $data['name']
    ]);

    $response = postJson('/api/admin/brands', $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name']);
});
