<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Nwidart\Modules\Facades\Module;

class ModuleController extends Controller
{
    /**
     * Display a listing of all modules.
     */
    public function index(): Response
    {
        $modules = Module::all();
        $formattedModules = [];

        foreach ($modules as $name => $module) {
            $moduleJson = json_decode(file_get_contents($module->getPath().'/module.json'), true);

            $formattedModules[] = [
                'name' => $moduleJson['name'],
                'alias' => $moduleJson['alias'] ?? $name,
                'description' => $moduleJson['description'] ?? '',
                'version' => $moduleJson['version'] ?? '1.0.1',
                'enabled' => $module->isEnabled(),
                'path' => $module->getPath(),
                'is_common' => $moduleJson['common'] ?? 0,
                'icon' => $moduleJson['icon_url'],
                'depends_on' => $moduleJson['depends_on'],
            ];
        }

        return Inertia::render('admin/modules/modules', [
            'modules' => $formattedModules,
        ]);
    }

    /**
     * Enable a specific module.
     */
    public function enable(string $name): RedirectResponse
    {
        if (! Module::has($name)) {
            return redirect()->back()->with('error', "Module {$name} not found");
        }

        if (Module::isEnabled($name)) {
            return redirect()->back()->with('info', "Module {$name} is already enabled");
        }

        Module::enable($name);

        return redirect()->back()->with('success', "Module {$name} has been enabled");
    }

    /**
     * Disable a specific module.
     */
    public function disable(string $name): RedirectResponse
    {
        if (! Module::has($name)) {
            return redirect()->back()->with('error', "Module {$name} not found");
        }

        if (! Module::isEnabled($name)) {
            return redirect()->back()->with('info', "Module {$name} is already disabled");
        }

        Module::disable($name);

        return redirect()->back()->with('success', "Module {$name} has been disabled");
    }
}
