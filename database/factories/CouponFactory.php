<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('COUPON-####'),
            'discount_percentage' => fake()->numberBetween(1, 90),
            'is_active' => true,
            'expires_at' => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
