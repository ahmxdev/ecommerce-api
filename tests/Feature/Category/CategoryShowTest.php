<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);


test('authenticated user can show a category', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $category = Category::create([
        'name' => 'CPUs',
    ]);

    $response = getJson("/api/admin/categories/{$category->id}");

    $response->assertOk();
    $response->assertJson([
        'data' => [
            'id' => $category->id,
            'name' => $category->name,
        ]
    ]);
});

test('guest cannot show a category', function () {
    $response = getJson("/api/admin/categories/1");

    $response->assertUnauthorized();
});

test('showing a non-existing category returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson("/api/admin/categories/99999");

    $response->assertNotFound();
});
