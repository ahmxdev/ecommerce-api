<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('admin can list categories', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $categories = Category::factory()->count(3)->create();

    $response = getJson('/api/categories');

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'parent'
            ]
        ]
    ]);
});

test('regular user cannot list categories', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson('/api/categories');

    $response->assertForbidden();
});

test('guest cannot list categories', function () {
    $response = getJson('/api/categories');

    $response->assertUnauthorized();
});
