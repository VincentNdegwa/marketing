<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Nwidart\Modules\Facades\Module;

class ModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-with-pages {name : The name of the module}
                            {--force : Force the operation to run when the module already exists}
                            {--plain : Generate a plain module (without example code)}
                            {--api : Generate an API module}
                            {--web : Generate a web module}
                            {--disabled : Do not enable the module at creation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module and register its pages for Inertia.js integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->info("Creating module: {$name}");
        
        // Build the command with all options
        $command = 'php artisan module:make ' . $name;
        
        if ($this->option('force')) {
            $command .= ' --force';
        }
        
        if ($this->option('plain')) {
            $command .= ' --plain';
        }
        
        if ($this->option('api')) {
            $command .= ' --api';
        }
        
        if ($this->option('web')) {
            $command .= ' --web';
        }
        
        if ($this->option('disabled')) {
            $command .= ' --disabled';
        }
        
        $this->line("Running: $command");
        
        // Run command with real-time output based on platform
        if ($this->isWindows()) {
            // Use passthru for Windows
            $exitCode = 0;
            passthru($command, $exitCode);
            $this->info('Wait for pages regsitration...');

            if ($exitCode !== 0) {
                $this->error("Failed to create module: {$name}");
                return Command::FAILURE;
            }
        } else {
            // Use TTY mode for macOS/Linux
            $process = Process::path(base_path())
                ->tty()
                ->run($command);
            $this->info('Wait for pages regsitration...');

            if (!$process->successful()) {
                $this->error("Failed to create module: {$name}");
                $this->error($process->errorOutput());
                return Command::FAILURE;
            }
        }
        
        // If the module was created successfully and not disabled, register its pages
        if (!$this->option('disabled')) {
            $this->info("Registering pages for module: {$name}");
            
            $registerCommand = 'php artisan modules:register-pages';
            $this->line("Running: $registerCommand");
            
            // Run command with real-time output based on platform
            if ($this->isWindows()) {
                // Use passthru for Windows
                $exitCode = 0;
                passthru($registerCommand, $exitCode);
                
                if ($exitCode !== 0) {
                    $this->error("Failed to register pages for module: {$name}");
                    return Command::FAILURE;
                }
            } else {
                // Use TTY mode for macOS/Linux
                $process = Process::path(base_path())
                    ->tty()
                    ->run($registerCommand);
                    
                if (!$process->successful()) {
                    $this->error("Failed to register pages for module: {$name}");
                    $this->error($process->errorOutput());
                    return Command::FAILURE;
                }
            }
        }
        
        $this->info("Module {$name} created and pages registered successfully!");
        return Command::SUCCESS;
    }
    
    /**
     * Check if the current platform is Windows
     */
    private function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
