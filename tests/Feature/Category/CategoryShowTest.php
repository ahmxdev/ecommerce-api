<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);


test('admin can show a category', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $category = Category::create([
        'name' => 'CPUs',
    ]);

    $response = getJson("/api/categories/{$category->id}");

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'id' => $category->id,
            'name' => $category->name,
        ]
    ]);
});

test('regular user cannot show a category', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $category = Category::create([
        'name' => 'CPUs',
    ]);

    $response = getJson("/api/categories/{$category->id}");

    $response->assertForbidden();
});

test('guest cannot show a category', function () {
    $response = getJson("/api/categories/1");

    $response->assertUnauthorized();
});

test('showing a non-existing category returns 404', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $response = getJson("/api/categories/99999");

    $response->assertNotFound();
});
