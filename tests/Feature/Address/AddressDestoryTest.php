<?php

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);


test('authenticated user can delete his addresses', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $user->id
    ]);

    $response = deleteJson("/api/addresses/{$address->id}");

    $response->assertNoContent();
    assertDatabaseMissing('addresses', [
        'id' => $address->id
    ]);
});

test('guest cannot delete an address', function () {

    $response = deleteJson("/api/addresses/1");

    $response->assertUnauthorized();
});

test('user cannot delete an address owned by another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Sanctum::actingAs($user);

    $address = Address::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = deleteJson("/api/addresses/{$address->id}");

    $response->assertNotFound();
});
