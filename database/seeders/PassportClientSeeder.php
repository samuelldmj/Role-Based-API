<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class PassportClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Checking if the personal access client exists
        $personalClientExists = DB::table('oauth_personal_access_clients')->exists();

        // Checking if the password grant client exists
        $passwordClientExists = DB::table('oauth_clients')
            ->where('password_client', 1)
            ->exists();

        //run passport:install if either client is missing
        // This prevents re-running it unnecessarily
        if (!$personalClientExists || !$passwordClientExists) {
            $this->command->info('Passport clients not found. Installing...');
            Artisan::call('passport:install');
            $this->command->info('Passport clients installed successfully.');
        } else {
            $this->command->info('Passport clients already exist. Skipping installation.');
        }
    }
}
