<?php

namespace Modules\GoogleSocialite\App\Database\Seeders;

use Illuminate\Database\Seeder;


class GoogleSocialiteDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class
        ]);
    }
}
