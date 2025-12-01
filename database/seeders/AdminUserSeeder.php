<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get admin role
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrator with full access',
                'is_active' => true,
            ]
        );

        // Create default admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to user
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);

        $this->command->info('Admin user created:');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: password');
        $this->command->info('Role: Admin (with all permissions)');
    }
}
