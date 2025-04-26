<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ModuleTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:test {module? : The module name to test (optional, if not provided will test all modules)} 
                           {--filter= : Filter which tests to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tests for specific module or all modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('module');
        $filter = $this->option('filter');
        
        if ($moduleName) {
            // Test a specific module
            $this->testModule($moduleName, $filter);
        } else {
            // Test all modules
            $this->testAllModules($filter);
        }

        return Command::SUCCESS;
    }

    /**
     * Test a specific module
     *
     * @param string $moduleName
     * @param string|null $filter
     * @return bool
     */
    protected function testModule(string $moduleName, ?string $filter = null): bool
    {
        $modulePath = base_path("Modules/{$moduleName}");
        
        if (!File::exists($modulePath)) {
            $this->error("Module '{$moduleName}' not found!");
            return false;
        }
        
        // Check if the module has tests directory
        $testsPath = "{$modulePath}/tests";
        if (!File::exists($testsPath)) {
            $this->warn("Module '{$moduleName}' does not have a tests directory. Creating one...");
            File::makeDirectory($testsPath);
            File::makeDirectory("{$testsPath}/Feature", 0755, true);
            File::makeDirectory("{$testsPath}/Unit", 0755, true);
        }
        
        $this->info("Running tests for {$moduleName} module...");
        
        // Use the main phpunit.xml file and filter by module name
        $phpunitPath = base_path('vendor/bin/phpunit');
        if (PHP_OS_FAMILY === 'Windows') {
            $phpunitPath = base_path('vendor\\bin\\phpunit.bat');
        }
        
        $command = [$phpunitPath, '--testsuite', 'Modules'];
        
        // Add module filter to only run tests from this module
        $moduleFilter = $filter ? "$moduleName.*$filter" : $moduleName;
        $command[] = '--filter';
        $command[] = $moduleFilter;
        
        return $this->runProcess($command);
    }
    
    /**
     * Test all modules
     *
     * @param string|null $filter
     * @return bool
     */
    protected function testAllModules(?string $filter = null): bool
    {
        $modulesPath = base_path('Modules');
        
        if (!File::exists($modulesPath)) {
            $this->error("Modules directory not found!");
            return false;
        }
        
        $modules = File::directories($modulesPath);
        
        if (empty($modules)) {
            $this->warn("No modules found in the Modules directory.");
            return false;
        }
        
        $this->info("Running tests for all modules...");
        
        // Use the main phpunit.xml file with the Modules testsuite
        $phpunitPath = base_path('vendor/bin/phpunit');
        if (PHP_OS_FAMILY === 'Windows') {
            $phpunitPath = base_path('vendor\\bin\\phpunit.bat');
        }
        
        $command = [$phpunitPath, '--testsuite', 'Modules'];
        
        if ($filter) {
            $command[] = '--filter';
            $command[] = $filter;
        }
        
        return $this->runProcess($command);
    }
    
    /**
     * Run a process and return whether it was successful
     *
     * @param array $command
     * @return bool
     */
    protected function runProcess(array $command): bool
    {
        $this->info('Running: ' . implode(' ', $command));
        
        $process = new Process($command, base_path());
        $process->setTimeout(null);
        $process->setTty(Process::isTtySupported());
        
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });
        
        return $process->isSuccessful();
    }
    

}
