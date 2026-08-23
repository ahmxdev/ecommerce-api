<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('authenticated user can list their orders', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Sanctum::actingAs($user);

    $userOrders = Order::factory()
        ->count(2)
        ->create(['user_id' => $user->id]);

    Order::factory()
        ->create(['user_id' => $otherUser->id]);

    $response = getJson('/api/orders');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment([
            'id' => $userOrders[0]->id,
        ])
        ->assertJsonFragment([
            'id' => $userOrders[1]->id,
        ]);
});


test('guest cannot list orders', function () {
    $response = getJson('/api/orders');

    $response->assertUnauthorized();
});
