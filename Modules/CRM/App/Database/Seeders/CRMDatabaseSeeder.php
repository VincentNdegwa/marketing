<?php

namespace Modules\CRM\App\Database\Seeders;

use Illuminate\Database\Seeder;


class CRMDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class
        ]);
    }
}
