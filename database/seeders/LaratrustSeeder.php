<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Business\app\Models\Business;

class LaratrustSeeder extends Seeder
{
    public function run(): void
    {
        // Create System Business
        $systemBusiness = Business::create([
            'name' => 'System Business',
            'slug' => 'system-business',
            'description' => 'Main business for the system administration',
            'email' => 'admin@system.com',
            'is_active' => true,
        ]);

        // Create a Client Business
        $clientBusiness = Business::create([
            'name' => 'Client Business',
            'slug' => 'client-business',
            'description' => 'Example client business',
            'email' => 'admin@client.com',
            'is_active' => true,
        ]);

        // Create Superadmin role (system-wide, no business_id)
        $superadmin = Role::create([
            'name' => 'superadmin',
            'display_name' => 'Super Administrator',
            'description' => 'User with access to all system features',
        ]);

        // Create admin role for the client business
        $clientAdmin = Role::create([
            'name' => 'admin',
            'display_name' => 'Client Administrator',
            'description' => 'Administrator of the client business',
            'business_id' => $clientBusiness->id,
        ]);

        // Create a superadmin user (should be only one in the system)
        $superadminUser = User::where('email', 'superadmin@example.com')->first();
        if (! $superadminUser) {
            $superadminUser = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Create a client admin user
        $clientAdminUser = User::where('email', 'admin@client.com')->first();
        if (! $clientAdminUser) {
            $clientAdminUser = User::create([
                'name' => 'Client Admin',
                'email' => 'admin@client.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Assign superadmin role (system-wide)
        $superadminUser->roles()->attach($superadmin);

        // Also give superadmin access to client business as admin
        $superadminUser->roles()->attach($clientAdmin->id, ['business_id' => $clientBusiness->id]);

        // Assign client admin role to client admin user
        $clientAdminUser->roles()->attach($clientAdmin->id, ['business_id' => $clientBusiness->id]);

        // Associate users with businesses
        $superadminUser->businesses()->attach($systemBusiness->id, ['is_default' => true]);
        $superadminUser->businesses()->attach($clientBusiness->id);
        $clientAdminUser->businesses()->attach($clientBusiness->id, ['is_default' => true]);
    }
}
