<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

test('admin can update a category', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $parent1 = Category::create([
        'name' => 'parent1',
    ]);
    $parent2 = Category::create([
        'name' => 'parent2',
    ]);

    $category = Category::create([
        'name' => 'CPUs',
        'parent_id' => $parent1->id
    ]);
    $data = [
        'name' => 'GPUs',
        'parent_id' => $parent2->id
    ];

    $response = putJson("/api/categories/{$category->id}", $data);

    $response->assertOk();
    assertDatabaseHas('categories', [
        'name' => $data['name'],
        'parent_id' => $data['parent_id']
    ]);
    $response->assertJsonPath('data.name', $data['name']);
    $response->assertJsonPath('data.parent.id', $data['parent_id']);
});

test('regular user cannot update a category', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $category = Category::create([
        'name' => 'CPUs',
    ]);

    $data = [
        'name' => 'GPUs',
    ];

    $response = putJson("/api/categories/{$category->id}", $data);

    $response->assertForbidden();
});

test('guest cannot update a category', function () {
    $data = [
        'name' => 'GPUs',
    ];

    $response = putJson("/api/categories/1", $data);

    $response->assertUnauthorized();
});

test('a category cannot be a child of its children', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $parent = Category::create([
        'name' => 'Computer Parts',
    ]);
    $category = Category::create([
        'name' => 'CPUs',
        'parent_id' => $parent->id
    ]);
    $data = [
        'parent_id' => $category->id
    ];

    $response = putJson("/api/categories/{$parent->id}", $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        'parent_id',
    ]);
    assertDatabaseHas('categories', [
        'id' => $parent->id,
        'parent_id' => null
    ]);
});

test('cannot update a not-existing category', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [
        'name' => 'new name'
    ];

    $response = putJson("/api/categories/99999", $data);

    $response->assertNotFound();
});
