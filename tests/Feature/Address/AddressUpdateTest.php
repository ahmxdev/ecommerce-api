<?php

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);


test('authinticated user can update an address', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $user->id
    ]);

    $data = [
        'country' => 'Egypt',
        'state' => 'Cairo',
        'city' => 'Cairo',
        'district' => 'Nasr City',
        'street' => 'Abbas El Akkad',
        'building' => '12',
        'floor' => '3',
        'apartment' => '12A',
        'landmark' => 'Near City Stars',
    ];

    $response = putJson("/api/addresses/{$address->id}", $data);

    $response->assertOk();
    $response->assertJsonFragment([
        'country' => 'Egypt',
        'state' => 'Cairo',
        'city' => 'Cairo',
        'district' => 'Nasr City',
        'street' => 'Abbas El Akkad',
        'building' => '12',
        'floor' => '3',
        'apartment' => '12A',
        'landmark' => 'Near City Stars',
    ]);
    assertDatabaseHas('addresses', [
        'user_id' => $user->id,
        'country' => $data['country'],
        'state' => $data['state'],
        'city' => $data['city'],
        'district' => $data['district'],
        'street' => $data['street'],
        'building' => $data['building'],
        'floor' => $data['floor'],
        'apartment' => $data['apartment'],
        'landmark' => $data['landmark'],
    ]);
});

test('guest cannot update an address', function () {

    $response = putJson('/api/addresses/1', []);

    $response->assertUnauthorized();
});
