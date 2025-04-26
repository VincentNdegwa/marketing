<?php

namespace Modules\UserManagement\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $current_business_id = session()->get('current_business_id');

        $roles = Role::where('business_id', $current_business_id)
            ->with(['permissions' => function ($query) use ($current_business_id) {
                $query->where('permissions.business_id', $current_business_id)
                    ->orWhereNull('permissions.business_id');
            }])
            ->get()
            // ->unique('name')
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description,
                    'permissions' => $role->permissions->pluck('display_name'),
                    'users_count' => $role->users()->count(),
                ];
            });

        return Inertia::module('usermanagement/roles/Index', [
            'roles' => $roles,
            'current_business_id' => $current_business_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $current_business_id = session()->get('current_business_id');

        $permissions = Permission::where('business_id', $current_business_id)
            ->orWhereNull('business_id')
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'description' => $permission->description,
                    'module' => $permission->module,
                ];
            });

        return Inertia::module('usermanagement/roles/Create', [
            'permissions' => $permissions,
            'current_business_id' => $current_business_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $current_business_id = session()->get('current_business_id');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($current_business_id) {
                    return $query->where('business_id', $current_business_id);
                }),
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'business_id' => 'required|exists:businesses,id',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
                'business_id' => $validated['business_id'],
            ]);

            if (isset($validated['permissions']) && count($validated['permissions']) > 0) {
                $role->permissions()->attach($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('user-role.index')
                ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to create role: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return Inertia::module('usermanagement/roles/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $current_business_id = session()->get('current_business_id');

        $role = Role::with('permissions')
            ->where('id', $id)
            ->where('business_id', $current_business_id)
            ->firstOrFail();

        $permissions = Permission::where('business_id', $current_business_id)
            ->orWhereNull('business_id')
            ->get()
            // ->unique('name')
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'description' => $permission->description,
                    'module' => $permission->module,
                ];
            });

        return Inertia::module('usermanagement/roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'permissions' => $role->permissions,
            ],
            'permissions' => $permissions,
            'current_business_id' => $current_business_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $current_business_id = session()->get('current_business_id');

        $role = Role::where('id', $id)
            // ->where('business_id', $current_business_id)
            ->firstOrFail();

        // Validate the request
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($current_business_id) {
                    return $query->where('business_id', $current_business_id);
                })->ignore($role->id),
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'business_id' => 'required|exists:businesses,id',
        ]);

        DB::beginTransaction();

        try {
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
            ]);

            // Sync permissions
            if (isset($validated['permissions'])) {
                $role->permissions()->sync($validated['permissions']);
            } else {
                $role->permissions()->detach();
            }

            DB::commit();

            return redirect()->route('user-role.index')
                ->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to update role: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $current_business_id = session()->get('current_business_id');

        $role = Role::where('id', $id)
            ->where('business_id', $current_business_id)
            ->firstOrFail();

        if ($role->name === 'admin') {
            return redirect()->route('user-role.index')
                ->with('error', 'The admin role cannot be deleted.');
        }
        DB::beginTransaction();

        try {
            $role->permissions()->detach();
            $role->users()->detach();
            $role->delete();

            DB::commit();

            return redirect()->route('user-role.index')
                ->with('success', 'Role deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('user-role.index')
                ->with('error', 'Failed to delete role: '.$e->getMessage());
        }
    }
}
