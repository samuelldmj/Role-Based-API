<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userRole = Role::where('slug', 'user')->first();

        User::factory()
            ->count(5)
            ->create()->each(function (User $user) use ($userRole) {
                $user->roles()->attach($userRole);
            });
    }
}
