<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin role with all permissions
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Super Administrator with full access',
            ]
        );
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));

        // Create Admin role
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrator with limited access',
            ]
        );
        $adminPermissions = Permission::whereIn('slug', [
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'assign-roles',
        ])->pluck('id');
        $admin->permissions()->sync($adminPermissions);

        // Create Manager role
        $manager = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Manager with user management access',
            ]
        );
        $managerPermissions = Permission::whereIn('slug', [
            'view-users',
            'create-users',
            'edit-users',
        ])->pluck('id');
        $manager->permissions()->sync($managerPermissions);

        // Create User role (default role)
        Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
                'description' => 'Regular user with basic access',
            ]
        );
    }
}
