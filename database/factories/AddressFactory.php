<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
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
            'country' => fake()->country(),
            'state' => fake()->state(),
            'city' => fake()->city(),
            'district' => fake()->streetName(),
            'street' => fake()->streetAddress(),
            'building' => (string) fake()->numberBetween(1, 100),
            'floor' => (string) fake()->numberBetween(1, 20),
            'apartment' => (string) fake()->numberBetween(1, 200),
            'landmark' => fake()->optional()->sentence(3),
        ];
    }
}
