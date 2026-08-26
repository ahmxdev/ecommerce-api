<?php

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);


test('regular user cannot list coupons', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson('/api/coupons');

    $response->assertForbidden();
});

test('admin can list coupons', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $coupons = Coupon::factory()->count(5)->create();

    $response = getJson('/api/coupons');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'code',
                'discount_percentage',
                'is_active',
                'expires_at',
            ]
        ]
    ]);
    $response->assertJsonPath('data.0.code', $coupons[0]->code);
});

test('guest cannot list coupons', function () {

    $response = getJson('/api/coupons');

    $response->assertUnauthorized();
});
