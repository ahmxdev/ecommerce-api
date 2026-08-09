<?php

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);


test('authenticated user can create a coupon', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $data = [
        'code' => 'SUMMER20',
        'discount_percentage' => 20,
        'is_active' => true,
        'expires_at' => now()->addMonth()->toDateTimeString(),
    ];

    $response = postJson('/api/admin/coupons', $data);

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


test('guest cannot create a coupon', function () {
    $response = postJson('/api/admin/coupons', []);

    $response->assertUnauthorized();
});
