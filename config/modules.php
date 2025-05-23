<?php

use Nwidart\Modules\Activators\FileActivator;
use Nwidart\Modules\Providers\ConsoleServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | Default module namespace.
    |
    */
    'namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Module Stubs
    |--------------------------------------------------------------------------
    |
    | Default module stubs.
    |
    */
    'stubs' => [
        'enabled' => true,
        'path' => base_path('stubs/nwidart-stubs'),
        'files' => [
            'routes/web' => 'routes/web.php',
            'routes/api' => 'routes/api.php',
            'views/app' => 'resources/views/app.blade.php',
            'views/master' => 'resources/views/layouts/master.blade.php',
            'scaffold/config' => 'config/config.php',
            'composer' => 'composer.json',
            'assets/js/app' => 'resources/js/app.ts',
            'assets/js/tsconfig' => 'resources/js/tsconfig.json',
            'tsconfig' => 'tsconfig.json',
            'assets/js/types' => 'resources/js/types.ts',
            'assets/js/pages/Index' => 'resources/js/pages/Index.vue',
            'assets/sass/app' => 'resources/assets/sass/app.scss',
            'vite' => 'vite.config.js',
            'package' => 'package.json',
            'listener/MenuListener' => 'App/Listeners/MenuListener.php',
            'seeders/PermissionTableSeeder' => 'App/Database/Seeders/PermissionTableSeeder.php',
        ],
        'replacements' => [
            /**
             * Define custom replacements for each section.
             * You can specify a closure for dynamic values.
             *
             * Example:
             *
             * 'composer' => [
             *      'CUSTOM_KEY' => fn (\Nwidart\Modules\Generators\ModuleGenerator $generator) => $generator->getModule()->getLowerName() . '-module',
             *      'CUSTOM_KEY2' => fn () => 'custom text',
             *      'LOWER_NAME',
             *      'STUDLY_NAME',
             *      // ...
             * ],
             *
             * Note: Keys should be in UPPERCASE.
             */
            'routes/web' => ['LOWER_NAME', 'STUDLY_NAME', 'PLURAL_LOWER_NAME', 'KEBAB_NAME', 'MODULE_NAMESPACE', 'CONTROLLER_NAMESPACE'],
            'routes/api' => ['LOWER_NAME', 'STUDLY_NAME', 'PLURAL_LOWER_NAME', 'KEBAB_NAME', 'MODULE_NAMESPACE', 'CONTROLLER_NAMESPACE'],
            'vite' => ['LOWER_NAME', 'STUDLY_NAME', 'KEBAB_NAME'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'KEBAB_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
            'views/app' => ['LOWER_NAME', 'STUDLY_NAME'],
            'views/master' => ['LOWER_NAME', 'STUDLY_NAME', 'KEBAB_NAME'],
            'scaffold/config' => ['STUDLY_NAME'],
            'composer' => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
                'PROVIDER_NAMESPACE',
                'APP_FOLDER_NAME',
            ],
            'listener/MenuListener' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'seeders/PermissionTableSeeder' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'event-provider' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],
            'scaffold/provider' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],
            'assets/js/app' => ['LOWER_NAME', 'STUDLY_NAME'],
            'assets/js/types' => ['LOWER_NAME', 'STUDLY_NAME'],
            'assets/js/tsconfig' => ['LOWER_NAME', 'STUDLY_NAME'],
            'tsconfig' => ['LOWER_NAME', 'STUDLY_NAME'],
            'assets/js/pages/Index' => ['LOWER_NAME', 'STUDLY_NAME'],
            'seeder' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],
            'migration/create' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],
            'migration/add' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],
            'migration/delete' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],
            'migration/drop' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],
            'migration/plain' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'NAMESPACE', 'CLASS'],

        ],
        'gitkeep' => true,
    ],
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Modules path
        |--------------------------------------------------------------------------
        |
        | This path is used to save the generated module.
        | This path will also be added automatically to the list of scanned folders.
        |
        */
        'modules' => base_path('Modules'),

        /*
        |--------------------------------------------------------------------------
        | Modules assets path
        |--------------------------------------------------------------------------
        |
        | Here you may update the modules' assets path.
        |
        */
        'assets' => public_path('modules'),

        /*
        |--------------------------------------------------------------------------
        | The migrations' path
        |--------------------------------------------------------------------------
        |
        | Where you run the 'module:publish-migration' command, where do you publish the
        | the migration files?
        |
        */
        'migration' => base_path('App/Database/Migrations'),

        /*
        |--------------------------------------------------------------------------
        | The app path
        |--------------------------------------------------------------------------
        |
        | app folder name
        | for example can change it to 'src' or 'App'
        */
        'app_folder' => 'App/',

        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Setting the generate key to false will not generate that folder
        */
        'generator' => [
            // app/
            'actions' => ['path' => 'App/Actions', 'generate' => false, 'namespace' => 'App\Actions'],
            'casts' => ['path' => 'App/Casts', 'generate' => false, 'namespace' => 'App\Casts'],
            'channels' => ['path' => 'App/Broadcasting', 'generate' => false, 'namespace' => 'App\Broadcasting'],
            'class' => ['path' => 'App/Classes', 'generate' => false, 'namespace' => 'App\Classes'],
            'command' => ['path' => 'App/Console', 'generate' => false, 'namespace' => 'App\Console'],
            'component-class' => ['path' => 'App/View/Components', 'generate' => false, 'namespace' => 'App\View\Components'],
            'emails' => ['path' => 'App/Emails', 'generate' => false, 'namespace' => 'App\Emails'],
            'event' => ['path' => 'App/Events', 'generate' => false, 'namespace' => 'App\Events'],
            'enums' => ['path' => 'App/Enums', 'generate' => false, 'namespace' => 'App\Enums'],
            'exceptions' => ['path' => 'App/Exceptions', 'generate' => false, 'namespace' => 'App\Exceptions'],
            'jobs' => ['path' => 'App/Jobs', 'generate' => false, 'namespace' => 'App\Jobs'],
            'helpers' => ['path' => 'App/Helpers', 'generate' => false, 'namespace' => 'App\Helpers'],
            'interfaces' => ['path' => 'App/Interfaces', 'generate' => false, 'namespace' => 'App\Interfaces'],
            'listener' => ['path' => 'App/Listeners', 'generate' => true, 'namespace' => 'App\Listeners'],
            'model' => ['path' => 'App/Models', 'generate' => false, 'namespace' => 'App\Models'],
            'notifications' => ['path' => 'App/Notifications', 'generate' => false, 'namespace' => 'App\Notifications'],
            'observer' => ['path' => 'App/Observers', 'generate' => false, 'namespace' => 'App\Observers'],
            'policies' => ['path' => 'App/Policies', 'generate' => false, 'namespace' => 'App\Policies'],
            'provider' => ['path' => 'App/Providers', 'generate' => true, 'namespace' => 'App\Providers'],
            'repository' => ['path' => 'App/Repositories', 'generate' => false, 'namespace' => 'App\Repositories'],
            'resource' => ['path' => 'App/Transformers', 'generate' => false, 'namespace' => 'App\Transformers'],
            'route-provider' => ['path' => 'App/Providers', 'generate' => true, 'namespace' => 'App\Providers'],
            'rules' => ['path' => 'App/Rules', 'generate' => false, 'namespace' => 'App\Rules'],
            'services' => ['path' => 'App/Services', 'generate' => false, 'namespace' => 'App\Services'],
            'scopes' => ['path' => 'App/Models/Scopes', 'generate' => false, 'namespace' => 'App\Models\Scopes'],
            'traits' => ['path' => 'App/Traits', 'generate' => false, 'namespace' => 'App\Traits'],

            // App/Http/
            'controller' => ['path' => 'App/Http/Controllers', 'generate' => true, 'namespace' => 'App\Http\Controllers'],
            'filter' => ['path' => 'App/Http/Middleware', 'generate' => false, 'namespace' => 'App\Http\Middleware'],
            'request' => ['path' => 'App/Http/Requests', 'generate' => false, 'namespace' => 'App\Http\Requests'],

            'config' => ['path' => 'config', 'generate' => true],
            'command' => ['path' => 'App/Console', 'generate' => false],
            'channels' => ['path' => 'App/Broadcasting', 'generate' => false],
            'migration' => ['path' => 'App/Database/Migrations', 'generate' => true, 'namespace' => 'App\\Database\\Migrations'],
            'seeder' => ['path' => 'App/Database/Seeders', 'generate' => true, 'namespace' => 'App\Database\Seeders'],
            'factory' => ['path' => 'App/Database/Factories', 'generate' => true, 'namespace' => 'App\Database\Factories'],

            // lang/
            'lang' => ['path' => 'lang', 'generate' => false],

            // resource/
            'assets' => ['path' => 'resources/assets', 'generate' => true],
            'component-view' => ['path' => 'resources/views/components', 'generate' => false],
            'views' => ['path' => 'resources/views', 'generate' => true],

            // routes/
            'routes' => ['path' => 'routes', 'generate' => true],

            // tests/
            'test-feature' => ['path' => 'tests/Feature', 'generate' => true],
            'test-unit' => ['path' => 'tests/Unit', 'generate' => true],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Discover of Modules
    |--------------------------------------------------------------------------
    |
    | Here you configure auto discover of module
    | This is useful for simplify module providers.
    |
    */
    'auto-discover' => [
        /*
        |--------------------------------------------------------------------------
        | Migrations
        |--------------------------------------------------------------------------
        |
        | This option for register migration automatically.
        |
        */
        'migrations' => true,

        /*
        |--------------------------------------------------------------------------
        | Translations
        |--------------------------------------------------------------------------
        |
        | This option for register lang file automatically.
        |
        */
        'translations' => false,

    ],

    /*
    |--------------------------------------------------------------------------
    | Package commands
    |--------------------------------------------------------------------------
    |
    | Here you can define which commands will be visible and used in your
    | application. You can add your own commands to merge section.
    |
    */
    'commands' => ConsoleServiceProvider::defaultCommands()
        ->merge([
            // New commands go here
        ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned. By default will scan vendor
    | directory. This is useful if you host the package in packagist website.
    |
    */
    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | Here is the config for the composer.json file, generated by this package
    |
    */
    'composer' => [
        'vendor' => env('MODULE_VENDOR', 'nwidart'),
        'author' => [
            'name' => env('MODULE_AUTHOR_NAME', 'Nicolas Widart'),
            'email' => env('MODULE_AUTHOR_EMAIL', 'n.widart@gmail.com'),
        ],
        'composer-output' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Choose what laravel-modules will register as custom namespaces.
    | Setting one to false will require you to register that part
    | in your own Service Provider class.
    |--------------------------------------------------------------------------
    */
    'register' => [
        'translations' => true,
        /**
         * load files on boot or register method
         */
        'files' => 'register',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activators
    |--------------------------------------------------------------------------
    |
    | You can define new types of activators here, file, database, etc. The only
    | required parameter is 'class'.
    | The file activator will store the activation status in storage/installed_modules
    */
    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => base_path('modules_statuses.json'),
        ], 
    ],

    'activator' => 'file',
];
