<?php

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);


test('admin can update a coupon', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $coupon = Coupon::factory()->create();

    $expiresAt = now()->addMonth();

    $data = [
        'code' => 'SUMMER30',
        'discount_percentage' => 30,
        'is_active' => true,
        'expires_at' => $expiresAt->toDateTimeString(),
    ];

    $response = putJson("/api/coupons/{$coupon->id}", $data);

    $response->assertOk();

    $response->assertJsonFragment([
        'code' => 'SUMMER30',
        'discount_percentage' => 30,
        'is_active' => true,
        'expires_at' => $expiresAt->toDateString(),
    ]);

    assertDatabaseHas('coupons', [
        'code' => $data['code'],
        'discount_percentage' => $data['discount_percentage'],
        'is_active' => $data['is_active'],
    ]);
});

test('regular user cannot update a coupon', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $coupon = Coupon::factory()->create();

    $response = putJson("/api/coupons/{$coupon->id}", []);

    $response->assertForbidden();
});

test('guest cannot update a coupon', function () {
    $response = putJson('/api/coupons/1', []);

    $response->assertUnauthorized();
});
