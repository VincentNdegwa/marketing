<?php

namespace Modules\CMS\App\Database\Seeders;

use Illuminate\Database\Seeder;

class CMSDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class
        ]);
    }
}
