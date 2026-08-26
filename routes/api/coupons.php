<?php

use App\Http\Controllers\Coupon\CouponController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('coupons', CouponController::class);
});
