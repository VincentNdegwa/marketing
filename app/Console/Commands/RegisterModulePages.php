<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Nwidart\Modules\Facades\Module;

class RegisterModulePages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:register-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register module pages for Inertia.js integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Registering module pages for Inertia.js integration...');

        // Get all enabled modules
        $modules = Module::allEnabled();
        $this->info('Found '.count($modules).' enabled modules: '.implode(', ', array_keys($modules)));

        // Run the npm script to register module pages
        $process = Process::path(base_path())
            ->run('npm run modules:register');

        if ($process->successful()) {
            $this->info('Module pages registered successfully!');
            $this->line($process->output());
        } else {
            $this->error('Failed to register module pages!');
            $this->error($process->errorOutput());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
