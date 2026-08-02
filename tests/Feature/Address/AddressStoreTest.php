<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);


test('authinticated user can create an address', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

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

    $response = postJson('/api/addresses', $data);

    $response->assertCreated();
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

test('guest cannot create an address', function () {

    $response = postJson('/api/addresses', []);

    $response->assertUnauthorized();
});
