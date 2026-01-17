<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Get or create admin role
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full system access',
                'is_system' => true,
            ]
        );

        // Create admin user with all fields
        User::create([
            'name' => 'webadmin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'), // password = admin
            'role_id' => $adminRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
