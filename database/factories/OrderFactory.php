<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'final_price' => fake()->randomFloat(2, 50, 1000),
            'discount_amount' => fake()->randomFloat(2, 0, 100),
            'status' => 'pending',
            'coupon_id' => null,
            'coupon_code' => null,
            'shipping_country' => 'Egypt',
            'shipping_state' => 'Sharqia',
            'shipping_city' => '10th of Ramadan',
            'shipping_district' => 'District 1',
            'shipping_street' => 'Main Street',
            'shipping_building' => '10',
            'shipping_floor' => null,
            'shipping_apartment' => null,
            'shipping_landmark' => null,
        ];
    }
}
