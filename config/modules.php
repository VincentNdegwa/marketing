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
        'enabled' => false,
        'path' => base_path('vendor/nwidart/laravel-modules/src/Commands/stubs'),
        'files' => [
            'routes/web' => 'routes/web.php',
            'routes/api' => 'routes/api.php',
            'views/index' => 'resources/views/index.blade.php',
            'views/master' => 'resources/views/layouts/master.blade.php',
            'scaffold/config' => 'config/config.php',
            'composer' => 'composer.json',
            'assets/js/app' => 'resources/assets/js/app.js',
            'assets/sass/app' => 'resources/assets/sass/app.scss',
            'vite' => 'vite.config.js',
            'package' => 'package.json',
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
            'views/index' => ['LOWER_NAME'],
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
        'migration' => base_path('database/migrations'),

        /*
        |--------------------------------------------------------------------------
        | The app path
        |--------------------------------------------------------------------------
        |
        | app folder name
        | for example can change it to 'src' or 'App'
        */
        'app_folder' => 'app/',

        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Setting the generate key to false will not generate that folder
        */
        'generator' => [
            // app/
            'actions' => ['path' => 'app/Actions', 'generate' => false, 'namespace' => 'app\Actions'],
            'casts' => ['path' => 'app/Casts', 'generate' => false, 'namespace' => 'app\Casts'],
            'channels' => ['path' => 'app/Broadcasting', 'generate' => false, 'namespace' => 'app\Broadcasting'],
            'class' => ['path' => 'app/Classes', 'generate' => false, 'namespace' => 'app\Classes'],
            'command' => ['path' => 'app/Console', 'generate' => false, 'namespace' => 'app\Console'],
            'component-class' => ['path' => 'app/View/Components', 'generate' => false, 'namespace' => 'app\View\Components'],
            'emails' => ['path' => 'app/Emails', 'generate' => false, 'namespace' => 'app\Emails'],
            'event' => ['path' => 'app/Events', 'generate' => false, 'namespace' => 'app\Events'],
            'enums' => ['path' => 'app/Enums', 'generate' => false, 'namespace' => 'app\Enums'],
            'exceptions' => ['path' => 'app/Exceptions', 'generate' => false, 'namespace' => 'app\Exceptions'],
            'jobs' => ['path' => 'app/Jobs', 'generate' => false, 'namespace' => 'app\Jobs'],
            'helpers' => ['path' => 'app/Helpers', 'generate' => false, 'namespace' => 'app\Helpers'],
            'interfaces' => ['path' => 'app/Interfaces', 'generate' => false, 'namespace' => 'app\Interfaces'],
            'listener' => ['path' => 'app/Listeners', 'generate' => false, 'namespace' => 'app\Listeners'],
            'model' => ['path' => 'app/Models', 'generate' => false, 'namespace' => 'app\Models'],
            'notifications' => ['path' => 'app/Notifications', 'generate' => false, 'namespace' => 'app\Notifications'],
            'observer' => ['path' => 'app/Observers', 'generate' => false, 'namespace' => 'app\Observers'],
            'policies' => ['path' => 'app/Policies', 'generate' => false, 'namespace' => 'app\Policies'],
            'provider' => ['path' => 'app/Providers', 'generate' => true, 'namespace' => 'app\Providers'],
            'repository' => ['path' => 'app/Repositories', 'generate' => false, 'namespace' => 'app\Repositories'],
            'resource' => ['path' => 'app/Transformers', 'generate' => false, 'namespace' => 'app\Transformers'],
            'route-provider' => ['path' => 'app/Providers', 'generate' => true, 'namespace' => 'app\Providers'],
            'rules' => ['path' => 'app/Rules', 'generate' => false, 'namespace' => 'app\Rules'],
            'services' => ['path' => 'app/Services', 'generate' => false, 'namespace' => 'app\Services'],
            'scopes' => ['path' => 'app/Models/Scopes', 'generate' => false, 'namespace' => 'app\Models\Scopes'],
            'traits' => ['path' => 'app/Traits', 'generate' => false, 'namespace' => 'app\Traits'],

            // app/Http/
            'controller' => ['path' => 'app/Http/Controllers', 'generate' => true, 'namespace' => 'app\Http\Controllers'],
            'filter' => ['path' => 'app/Http/Middleware', 'generate' => false, 'namespace' => 'app\Http\Middleware'],
            'request' => ['path' => 'app/Http/Requests', 'generate' => false, 'namespace' => 'app\Http\Requests'],

            // config/
            'config' => ['path' => 'config', 'generate' => true],

            // database/
            'factory' => ['path' => 'database/factories', 'generate' => true],
            'migration' => ['path' => 'database/migrations', 'generate' => true],
            'seeder' => ['path' => 'database/seeders', 'generate' => true],

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
