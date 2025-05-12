<?php

namespace Modules\Marketing\App\Database\Seeders;

use Illuminate\Database\Seeder;


class MarketingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class
        ]);
    }
}
