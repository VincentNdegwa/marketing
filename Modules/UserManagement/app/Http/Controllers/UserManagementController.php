<?php

namespace Modules\UserManagement\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Modules\Business\App\Models\Business;
use Modules\Business\App\Models\UserBusiness;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $current_business_id = session()->get('current_business_id');
        
        // Get all users associated with the current business
        $users = User::whereHas('businesses', function($query) use ($current_business_id) {
            $query->where('businesses.id', $current_business_id);
        })->with(['roles' => function($query) use ($current_business_id) {
            $query->where('business_id', $current_business_id);
        }])->get();
        
        // Transform the data to include only necessary information
        $users = $users->map(function($user) use ($current_business_id) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'job_title' => $user->job_title,
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'roles' => $user->roles->pluck('display_name'),
                'is_admin' => $user->isAdminForBusiness($current_business_id)
            ];
        });
        
        return Inertia::module('usermanagement/users/Index', [
            'users' => $users,
            'current_business_id' => $current_business_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $current_business_id = session()->get('current_business_id');
        
        $roles = Role::where('business_id', $current_business_id)
            ->get()
            ->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                ];
            });
        
        return Inertia::module('usermanagement/users/Create', [
            'roles' => $roles,
            'current_business_id' => $current_business_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $current_business_id = session()->get('current_business_id');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'type' => 'required|string|in:client,staff,partner',
            'job_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'roles' => 'array',
            'is_active' => 'boolean',
            'business_id' => 'required|exists:businesses,id'
        ]);
        
        // Create the user
        DB::beginTransaction();
        
        try {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'type' => $validated['type'],
                'job_title' => $validated['job_title'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);
            
            // Attach the user to the business
            $user->businesses()->attach($validated['business_id'], [
                'is_default' => true
            ]);
            
            // Attach roles if any
            if (isset($validated['roles']) && count($validated['roles']) > 0) {
                foreach ($validated['roles'] as $roleId) {
                    $role = Role::findOrFail($roleId);
                    // Only attach roles from the current business
                    if ($role->business_id == $current_business_id) {
                        $user->attachRole($role, $current_business_id);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('usermanagement.index')
                ->with('success', 'User created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return Inertia::module('usermanagement/users/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $current_business_id = session()->get('current_business_id');
        
        // Get the user with their roles for the current business
        $user = User::with(['roles' => function($query) use ($current_business_id) {
                $query->where('business_id', $current_business_id);
            }])
            ->whereHas('businesses', function($query) use ($current_business_id) {
                $query->where('businesses.id', $current_business_id);
            })
            ->findOrFail($id);
        
        // Get all roles for the current business
        $roles = Role::where('business_id', $current_business_id)
            ->get()
            ->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                ];
            });
        
        return Inertia::module('usermanagement/users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
                'job_title' => $user->job_title,
                'bio' => $user->bio,
                'is_active' => $user->is_active,
                'roles' => $user->roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                    ];
                }),
            ],
            'roles' => $roles,
            'current_business_id' => $current_business_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        $current_business_id = session()->get('current_business_id');
        
        // Get the user
        $user = User::whereHas('businesses', function($query) use ($current_business_id) {
                $query->where('businesses.id', $current_business_id);
            })
            ->findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => $request->filled('password') ? ['required', 'confirmed', Password::defaults()] : '',
            'type' => 'required|string|in:client,staff,partner',
            'job_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'roles' => 'array',
            'is_active' => 'boolean',
            'business_id' => 'required|exists:businesses,id'
        ]);
        
        // Update the user
        DB::beginTransaction();
        
        try {
            // Update user details
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'type' => $validated['type'],
                'job_title' => $validated['job_title'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);
            
            // Update password if provided
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }
            
            // Sync roles for this business
            // First, detach all roles for this business
            $user->roles()->wherePivot('business_id', $current_business_id)->detach();
            
            // Then attach the new roles
            if (isset($validated['roles']) && count($validated['roles']) > 0) {
                foreach ($validated['roles'] as $roleId) {
                    $role = Role::findOrFail($roleId);
                    // Only attach roles from the current business
                    if ($role->business_id == $current_business_id) {
                        $user->attachRole($role, $current_business_id);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('usermanagement.index')
                ->with('success', 'User updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        $current_business_id = session()->get('current_business_id');
        
        // Get the user
        $user = User::whereHas('businesses', function($query) use ($current_business_id) {
                $query->where('businesses.id', $current_business_id);
            })
            ->findOrFail($id);
        
        // Delete the user
        DB::beginTransaction();
        
        try {
            // Detach roles for this business
            $user->roles()->wherePivot('business_id', $current_business_id)->detach();
            
            // Detach permissions for this business
            $user->permissions()->wherePivot('business_id', $current_business_id)->detach();
            
            // If this is the only business the user belongs to, soft delete the user
            if ($user->businesses()->count() <= 1) {
                $user->delete(); // This is a soft delete due to SoftDeletes trait
            }
            
            // Detach the user from this business
            $user->businesses()->detach($current_business_id);
            
            DB::commit();
            
            return redirect()->route('usermanagement.index')
                ->with('success', 'User removed successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('usermanagement.index')
                ->with('error', 'Failed to remove user: ' . $e->getMessage());
        }
    }
}
