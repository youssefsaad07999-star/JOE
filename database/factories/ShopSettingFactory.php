<?php

namespace Database\Factories;

use App\Models\ShopSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopSetting>
 */
class ShopSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'value' => $this->faker->word(),
            'type' => 'string',
        ];
    }
}
