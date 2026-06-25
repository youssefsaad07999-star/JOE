<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
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
            'order_id' => Order::factory(),
            'transaction_id' => fake()->optional()->bothify('pas_trn_#############'), // Simulates an online gateway string format
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'amount' => fake()->randomFloat(2, 10, 2000), // Min 10, Max 2000
            'method' => fake()->randomElement(['cod', 'card']),
        ];
    }

    /**
     * State transformation for a completed payment.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * State transformation for a pending payment (like initial COD).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * State transformation tailored specifically for Cash on Delivery.
     */
    public function cod(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'cash on delivery',
            'transaction_id' => null,
        ]);
    }

    /**
     * State transformation tailored specifically for Card transactions.
     */
    public function card(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'card',
            'transaction_id' => 'ch_'.fake()->unique()->alphanumeric(14),
        ]);
    }
}
