<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ShippingMethod;
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
            'address_id' => null,
            'shipping_method_id' => ShippingMethod::factory(),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'total_price' => fake()->randomFloat(2, 20, 2000), // Min 20, Max 2000
            'shipping_first_name' => fake()->firstName(),
            'shipping_last_name' => fake()->lastName(),
            'shipping_address' => fake()->streetAddress(),
            'shipping_address2' => fake()->optional()->secondaryAddress(),
            'shipping_city' => fake()->city(),
            'shipping_postal_code' => fake()->postcode(),
            'shipping_country' => fake()->countryCode(), // Generates 2-letter codes like 'EG' to match your database
            'shipping_phone' => fake()->phoneNumber(),
            'shipping_method' => 'Standard Shipping',
            'shipping_cost' => fake()->randomFloat(2, 0, 50),
        ];
    }

    /**
     * State transformation for a pending order.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * State transformation for a processing order (e.g., Cash on Delivery).
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    /**
     * State transformation for a cancelled order.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
