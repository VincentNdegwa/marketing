<?php

namespace Modules\Business\App\Database\Seeders;

use Illuminate\Database\Seeder;


class BusinessDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // PermissionTableSeeder::class
        ]);
    }
}
