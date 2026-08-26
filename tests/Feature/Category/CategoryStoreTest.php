<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('admin can create a category', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $parent = Category::factory()->create();

    $data = [
        'name' => 'CPUs',
        'parent_id' => $parent->id
    ];

    $response = postJson('/api/categories', $data);

    $response->assertCreated();
    assertDatabaseHas('categories', [
        'name' => $data['name'],
        'parent_id' => $data['parent_id']
    ]);
    $response->assertJsonPath('data.name', $data['name']);
    $response->assertJsonPath('data.parent.id', $data['parent_id']);
});

test('regular user cannot create a category', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = postJson('/api/categories', [
        'name' => 'CPUs',
    ]);

    $response->assertForbidden();
});

test('guest cannot create a category', function () {
    $data = [
        'name' => 'CPUs',
        // parent_id is nullable
    ];

    $response = postJson('/api/categories', $data);

    $response->assertUnauthorized();
});

test('validation fails when name is missing', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [];

    $response = postJson('/api/categories', $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name']);
});

test('validation fails when name already exists', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [
        'name' => 'CPUs',
        // parent_id is nullable
    ];

    Category::factory()->create([
        'name' => $data['name']
    ]);

    $response = postJson('/api/categories', $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name']);
});

test('parent is nullable', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [
        'name' => 'CPUs',
        // parent_id is nullable
    ];

    $response = postJson('/api/categories', $data);

    $response->assertCreated();
});

test('parent_id must exist', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [
        'name' => 'CPUs',
        'parent_id' => 999999
    ];

    $response = postJson('/api/categories', $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['parent_id']);
});
