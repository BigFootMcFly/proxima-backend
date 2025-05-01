<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //NOTE: In docker the seeding may be re-run on each restart, so we only create the admin user if it isn't already present

        //TODO: add amin rule and check for existing user with admin role insted of the name

        // skip, if the admin user already exists
        if (null !== User::where('name', 'admin')->first()) {
            return;
        }

        // get the admin email
        $adminEmail = config('proxima.setup.admin_email');
        if (null === $adminEmail) {
            Log::error('Environment variable "ADMIN_EMAIL" could not be found. Aborting.');
            exit(1);
        }

        // get the admin password
        $adminPassword = config('proxima.setup.admin_password');
        if (null === $adminPassword) {
            Log::error('Environment variable "ADMIN_PASSWORD" could not be found. Aborting.');
            exit(1);
        }

        // create the admin user
        User::create([
            'name' => 'admin',
            'email' => $adminEmail,
            'password' => bcrypt($adminPassword),
        ]);

    }
}
