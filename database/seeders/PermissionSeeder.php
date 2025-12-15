<?php
// database/seeders/PermissionSeeder.php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User permissions
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view users list'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'description' => 'Can edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users'],
            ['name' => 'Assign Roles', 'slug' => 'assign-roles', 'description' => 'Can assign roles to users'],

            // Role permissions
            ['name' => 'View Roles', 'slug' => 'view-roles', 'description' => 'Can view roles list'],
            ['name' => 'Create Roles', 'slug' => 'create-roles', 'description' => 'Can create new roles'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles', 'description' => 'Can edit existing roles'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles', 'description' => 'Can delete roles'],
            ['name' => 'Assign Permissions', 'slug' => 'assign-permissions', 'description' => 'Can assign permissions to roles'],

            // Permission management
            ['name' => 'View Permissions', 'slug' => 'view-permissions', 'description' => 'Can view permissions list'],
            ['name' => 'Create Permissions', 'slug' => 'create-permissions', 'description' => 'Can create new permissions'],
            ['name' => 'Edit Permissions', 'slug' => 'edit-permissions', 'description' => 'Can edit existing permissions'],
            ['name' => 'Delete Permissions', 'slug' => 'delete-permissions', 'description' => 'Can delete permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
