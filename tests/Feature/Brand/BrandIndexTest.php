<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('admin can list brands', function () {

    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);
    $brands = Brand::factory()->count(5)->create();

    $response = getJson('/api/brands');

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
            ],
        ],
    ]);
    $response->assertJsonFragment([
        'name' => $brands->first()->name,
    ]);
});

test('regular user cannot list brands', function () {

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson('/api/brands');

    $response->assertForbidden();
});

test('guest cannot list brands', function () {
    $response = $this->getJson('/api/brands');

    $response->assertUnauthorized();
});
