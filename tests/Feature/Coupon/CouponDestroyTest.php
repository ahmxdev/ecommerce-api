<?php

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);


test('admin can delete a coupon', function () {
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin);

    $coupon = Coupon::factory()->create();

    $response = deleteJson("/api/coupons/{$coupon->id}");

    $response->assertNoContent();
    assertDatabaseMissing('coupons', [
        'id' => $coupon->id
    ]);
});

test('regular user cannot delete a coupon', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $coupon = Coupon::factory()->create();

    $response = deleteJson("/api/coupons/{$coupon->id}");

    $response->assertForbidden();
});

test('guest cannot delete a coupon', function () {
    $response = deleteJson("/api/coupons/1");

    $response->assertUnauthorized();
});
