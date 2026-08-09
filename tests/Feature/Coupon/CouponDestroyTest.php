<?php

use App\Models\Address;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);


test('authinticated user can delete a coupon', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $coupon = Coupon::factory()->create();

    $response = deleteJson("/api/admin/coupons/{$coupon->id}");

    $response->assertNoContent();
    assertDatabaseMissing('coupons', [
        'id' => $coupon->id
    ]);
});

test('guest cannot delete a coupon', function () {

    $response = deleteJson("/api/admin/coupons/1");

    $response->assertUnauthorized();
});
