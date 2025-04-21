<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LaratrustSeeder extends Seeder
{
    public function run(): void
    {
        $systemBusiness = Business::create([
            'name' => 'System Business',
            'slug' => 'system-business',
            'description' => 'Main business for the system administration',
            'email' => 'admin@system.com',
            'is_active' => true,
        ]);

        $superadmin = Role::create([
            'name' => 'superadmin',
            'display_name' => 'Super Administrator',
            'description' => 'User with access to all system features',
        ]);

        // Create admin role for the system business (for Super Admin)
        $systemAdmin = Role::create([
            'name' => 'admin',
            'display_name' => 'System Administrator',
            'description' => 'Administrator of the system business',
            'business_id' => $systemBusiness->id,
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

        // Assign superadmin role (system-wide)
        $superadminUser->roles()->attach($superadmin);

        // Assign admin role for the system business
        $superadminUser->roles()->attach($systemAdmin->id, ['business_id' => $systemBusiness->id]);

        // Attach the system business to the superadmin user as default
        $superadminUser->businesses()->attach($systemBusiness, ['is_default' => true]);
    }
}
