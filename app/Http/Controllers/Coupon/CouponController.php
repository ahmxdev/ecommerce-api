<?php

namespace App\Http\Controllers\Coupon;

use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Http\Resources\Coupon\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController
{
    public function index()
    {
        $coupons = Coupon::paginate();
        return CouponResource::collection($coupons);
    }
    public function store(StoreCouponRequest $request)
    {
        $coupon = Coupon::create($request->validated());
        return new CouponResource($coupon);
    }
    // public function show(string $id) {}
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());
        return new CouponResource($coupon);
    }
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->noContent();
    }
}
