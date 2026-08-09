<?php

use App\Http\Controllers\Coupon\CouponController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('coupons', CouponController::class);
    });
});
