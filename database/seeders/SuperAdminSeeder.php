<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        // 2. Create the Super Admin user if they don't already exist
        $user = User::firstOrCreate(
            ['email' => 'youssefsaad07999@gmail.com'],
            [
                'first_name' => 'Youssef',
                'last_name' => 'Saad',
                'date_of_birth' => now()->subYears(25)->toDateString(),
                'phone_number' => '01040914614',
                'password' => Hash::make('14112001S@@d00'), // Change this password!
                'email_verified_at' => now(),
            ]
        );

        // 3. Assign the role to the user
        $user->assignRole($role);
    }
}
