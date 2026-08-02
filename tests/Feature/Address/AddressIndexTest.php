<?php

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);


test('authinticated user can list his addresses', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $addresses = Address::factory()->count(5)->create([
        'user_id' => $user->id
    ]);

    $response = getJson('/api/addresses');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'country',
                'state',
                'city',
                'district',
                'street',
                'building',
                'floor',
                'apartment',
                'landmark',
            ]
        ]
    ]);
    $response->assertJsonPath('data.0.country', $addresses[0]->country);
});

test('guest cannot list addresses', function () {

    $response = getJson('/api/addresses');

    $response->assertUnauthorized();
});
