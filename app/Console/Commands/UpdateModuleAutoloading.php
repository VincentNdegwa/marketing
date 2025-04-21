<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateModuleAutoloading extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:update-autoloading';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update composer.json with PSR-4 autoloading entries for all modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating module autoloading entries...');

        // Path to modules directory
        $modulesPath = base_path('Modules');
        
        // Get all module directories
        $modules = File::directories($modulesPath);
        
        // Path to composer.json
        $composerJsonPath = base_path('composer.json');
        
        // Read composer.json
        $composerJson = json_decode(File::get($composerJsonPath), true);
        
        // Get current autoload PSR-4 entries
        $psr4 = $composerJson['autoload']['psr-4'] ?? [];
        
        // Keep track of existing entries
        $existingEntries = [];
        
        // Add/update module entries
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            
            // Check if module has app directory
            if (File::isDirectory("$modulePath/app")) {
                $namespace = "Modules\\\\$moduleName\\\\App\\\\";
                $path = "Modules/$moduleName/app/";
                
                $psr4[$namespace] = $path;
                $existingEntries[] = $namespace;
                
                $this->info("Added autoloading for $moduleName: $namespace => $path");
            }
        }
        
        // Update composer.json
        $composerJson['autoload']['psr-4'] = $psr4;
        
        // Write back to composer.json
        File::put($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        
        $this->info('Module autoloading entries updated successfully.');
        $this->info('Run "composer dump-autoload" to apply changes.');
    }
}
