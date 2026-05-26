<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingMethod::insert([
            [
                'name' => 'Standard Shipping',
                'delivery_time' => '5–7 business days',
                'price' => 9.99,
                'is_active' => true,
            ],
            [
                'name' => 'Express Shipping',
                'delivery_time' => '2–3 business days',
                'price' => 19.99,
                'is_active' => true,
            ],
            [
                'name' => 'Overnight',
                'delivery_time' => 'Next business day',
                'price' => 39.99,
                'is_active' => true,
            ],
        ]);
    }
}
