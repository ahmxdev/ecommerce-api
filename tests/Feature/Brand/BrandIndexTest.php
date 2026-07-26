<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('authenticated user can list brands', function () {

    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $brands = Brand::factory()->count(5)->create();

    $response = getJson('/api/admin/brands');
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

test('guest cannot list brands', function () {
    $response = $this->getJson('/api/admin/brands');

    $response->assertUnauthorized();
});
