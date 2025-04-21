<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunModuleSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:run-seeder {module} {seeder?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a specific seeder from a module with App namespace support';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        $seederName = $this->argument('seeder') ?: $module.'DatabaseSeeder';

        $seederClass = "Modules\\{$module}\\App\\Database\\Seeders\\{$seederName}";

        $this->info("Running seeder: {$seederClass}");

        if (! class_exists($seederClass)) {
            $this->error("Seeder class {$seederClass} does not exist.");

            return 1;
        }

        $seeder = new $seederClass;
        $seeder->run();

        $this->info("Seeder {$seederClass} executed successfully.");

        return 0;
    }
}
