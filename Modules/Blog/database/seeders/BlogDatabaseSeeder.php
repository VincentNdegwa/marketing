<?php

namespace Modules\Blog\App\Database\Seeders;

use Illuminate\Database\Seeder;

class BlogDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
        ]);
    }
}
