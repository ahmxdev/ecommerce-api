<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('authenticated user can list categories', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $categories = Category::factory()->count(3)->create();

    $response = getJson('/api/admin/categories');

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

test('guest cannot list categories', function () {
    $response = getJson('/api/admin/categories');

    $response->assertUnauthorized();
});
