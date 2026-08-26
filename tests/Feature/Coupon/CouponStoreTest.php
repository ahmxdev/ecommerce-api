<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);


test('admin can create a coupon', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $data = [
        'code' => 'SUMMER20',
        'discount_percentage' => 20,
        'is_active' => true,
        'expires_at' => now()->addMonth()->toDateTimeString(),
    ];

    $response = postJson('/api/coupons', $data);

    $response->assertCreated();

    $response->assertJsonFragment([
        'code' => 'SUMMER20',
        'discount_percentage' => 20,
        'is_active' => true,
        'expires_at' => now()->addMonth()->toDateString(),
    ]);

    assertDatabaseHas('coupons', [
        'code' => $data['code'],
        'discount_percentage' => $data['discount_percentage'],
        'is_active' => $data['is_active'],
    ]);
});

test('regular user cannot create a coupon', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = postJson('/api/coupons', []);

    $response->assertForbidden();
});

test('guest cannot create a coupon', function () {
    $response = postJson('/api/coupons', []);

    $response->assertUnauthorized();
});
