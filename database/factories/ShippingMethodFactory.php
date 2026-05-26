<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingMethod>
 */
class ShippingMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'delivery_time' => $this->faker->randomElement([
                '5–7 business days',
                '2–3 business days',
                'Next business day',
            ]),
            'price' => $this->faker->randomElement([9.99, 19.99, 39.99]),
            'is_active' => true,
        ];
    }
}
