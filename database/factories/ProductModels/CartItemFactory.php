<?php

namespace Database\Factories\ProductModels;

use App\Models\ProductModels\CartItem;
use App\Models\ProductModels\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'session_id' => null,
            'product_variant_id' => ProductVariant::factory(),
            'quantity' => 1,
        ];
    }
}
