<?php

namespace App\Services\Order;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function checkout(User $user, int $addressId, ?int $couponId = null)
    {
        return DB::transaction(function () use ($user, $addressId, $couponId) {
            // ADDRESS
            $address = $user->addresses()->find($addressId);
            if (! $address) {
                throw new \Exception('Address not found.');
            }

            // CART
            $cart = $user->cart;
            if (! $cart) {
                throw new \Exception('Cart not found.');
            }

            $cart->load('items');
            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty.');
            }

            // SUB_TOTAL CALCULATION
            $subtotal = 0;
            $products = collect(); // to avoid N+1 problem later
            foreach ($cart->items as $item) {
                $product = Product::whereKey($item->product_id)
                    ->lockForUpdate()
                    ->first();
                $products->put($item->product_id, $product);

                if (! $product) {
                    throw new \Exception('Product no longer exists.');
                }
                if ($item->quantity > $product->stock) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                $subtotal += $product->price * $item->quantity;
            }

            // COUPON
            $discountAmount = 0;
            $coupon = null;
            if ($couponId) {
                $coupon = Coupon::findOrFail($couponId);
                if (! $coupon->is_active || $coupon->expires_at->isPast()) {
                    throw new \Exception('Coupon is not valid.');
                }

                $discountAmount = $subtotal * ($coupon->discount_percentage / 100);
            }

            // FINAL PRICE
            $finalPrice = $subtotal - $discountAmount;

            // CREATE ORDER
            $order = Order::create([
                'user_id' => $user->id,

                'final_price' => $finalPrice,
                'discount_amount' => $discountAmount,

                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,

                'status' => 'pending',

                'shipping_country' => $address->country,
                'shipping_state' => $address->state,
                'shipping_city' => $address->city,
                'shipping_district' => $address->district,
                'shipping_street' => $address->street,
                'shipping_building' => $address->building,

                'shipping_floor' => $address->floor,
                'shipping_apartment' => $address->apartment,
                'shipping_landmark' => $address->landmark,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'item_name' => $products[$item->product_id]->name,
                    'item_description' => $products[$item->product_id]->description,
                    'item_price' => $products[$item->product_id]->price,
                ]);
            }

            // DECREAMENT STOCK
            foreach ($cart->items as $item) {
                $products[$item->product_id]->decrement(
                    'stock',
                    $item->quantity
                );
            }

            // RESET CART ITEMS
            $cart->items()->delete();


            return $order;
        });
    }
}
