<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::insert([
            ['code' => 'US', 'name' => 'United States', 'is_active' => true],
            ['code' => 'GB', 'name' => 'United Kingdom', 'is_active' => true],
            ['code' => 'EG', 'name' => 'Egypt', 'is_active' => true],
            ['code' => 'CA', 'name' => 'Canada', 'is_active' => true],
            ['code' => 'AU', 'name' => 'Australia', 'is_active' => true],
            ['code' => 'DE', 'name' => 'Germany', 'is_active' => true],
            ['code' => 'FR', 'name' => 'France', 'is_active' => true],
        ]);
    }
}
