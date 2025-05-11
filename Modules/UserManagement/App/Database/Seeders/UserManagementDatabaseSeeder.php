<?php

namespace Modules\UserManagement\App\Database\Seeders;

use Illuminate\Database\Seeder;

class UserManagementDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
        ]);
    }
}
