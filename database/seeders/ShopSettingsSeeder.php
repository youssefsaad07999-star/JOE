<?php

namespace Database\Seeders;

use App\Models\ShopSetting;
use Illuminate\Database\Seeder;

class ShopSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShopSetting::insert([
            [
                'key' => 'free_shipping_threshold',
                'value' => '2000',
                'type' => 'int',
            ],
            [
                'key' => 'tax_rate',
                'value' => '14',
                'type' => 'decimal',
            ],
            [
                'key' => 'currency',
                'value' => 'EGP',
                'type' => 'string',
            ],
            [
                'key' => 'support_email',
                'value' => 'youssefsaad07999@gmail.com',
                'type' => 'string',
            ],
        ]);
    }
}
