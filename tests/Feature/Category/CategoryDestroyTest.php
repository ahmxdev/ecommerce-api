<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

test('authenticated user can delete a category', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $category = Category::factory()->create();

    $response = deleteJson("/api/categories/{$category->id}");

    $response->assertNoContent();
    assertDatabaseMissing('categories', [
        'id' => $category->id
    ]);
});

test('guest cannot delete a category', function () {

    $response = deleteJson("/api/categories/1");

    $response->assertUnauthorized();
});

test('cannot delete a category has children', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $category = Category::factory()->create();
    $child = Category::factory()->create([
        'parent_id' => $category->id
    ]);

    $response = deleteJson("/api/categories/{$category->id}");

    $response->assertConflict();
});

test('cannot delete a not-existing category', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = deleteJson("/api/categories/99999");

    $response->assertNotFound();
});
