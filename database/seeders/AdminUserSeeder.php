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
        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->roles()->sync(Role::where('slug', 'super-admin')->pluck('id'));

        // Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->roles()->sync(Role::where('slug', 'admin')->pluck('id'));

        // Create Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'phone' => '+1234567892',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $manager->roles()->sync(Role::where('slug', 'manager')->pluck('id'));

        // Create Regular user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'phone' => '+1234567893',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $user->roles()->sync(Role::where('slug', 'user')->pluck('id'));
    }
}
