<?php

namespace Database\Factories\ProductModels;

use App\Models\Model;
use App\Models\ProductModels\Category;
use App\Models\ProductModels\Fit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => str_replace('_', '-', $this->faker->words(3, true)),
            'description' => $this->faker->sentence(4),
            'category_id' => Category::doesntHave('children')->inRandomOrder()->first()->id,
            'fit_id' => Fit::inRandomOrder()->first()->id,
            'base_price' => $this->faker->randomFloat(2, 200, 1000),
        ];
    }
}
