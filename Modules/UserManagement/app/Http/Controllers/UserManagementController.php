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
            ->where('name', '!=', 'admin')
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
            'password' => 'required|string|min:8',
            'type' => 'required|string|in:subuser,client,staff,partner',
            'mobile' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'website' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
            'business_id' => 'required|exists:businesses,id'
        ]);
        
        // Create the user
        DB::beginTransaction();
        
        try {
            // Create the user with all available fields
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'type' => $validated['type'],
                'mobile' => $validated['mobile'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'address_line1' => $validated['address_line1'] ?? null,
                'address_line2' => $validated['address_line2'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'website' => $validated['website'] ?? null,
                'social_links' => $validated['social_links'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);
            
            $user->businesses()->attach($validated['business_id'], [
                'is_default' => true
            ]);
            
            if (isset($validated['roles']) && count($validated['roles']) > 0) {
                foreach ($validated['roles'] as $roleId) {
                    $role = Role::findOrFail($roleId);

                    if ($role->business_id == $current_business_id) {
                        $user->roles()->attach($role);
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
            ->where('name', '!=', 'admin')
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
                'mobile' => $user->mobile,
                'job_title' => $user->job_title,
                'bio' => $user->bio,
                'address_line1' => $user->address_line1,
                'address_line2' => $user->address_line2,
                'city' => $user->city,
                'state' => $user->state,
                'postal_code' => $user->postal_code,
                'country' => $user->country,
                'date_of_birth' => $user->date_of_birth,
                'gender' => $user->gender,
                'website' => $user->website,
                'social_links' => $user->social_links,
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
    public function update(Request $request, $id)
    {
        $current_business_id = session()->get('current_business_id');
        
        $user = User::whereHas('businesses', function($query) use ($current_business_id) {
                $query->where('businesses.id', $current_business_id);
            })
            ->findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'type' => 'required|string|in:subuser,client,staff,partner',
            'mobile' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'website' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ];
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
        }
        
        $validated = $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            // Update user with all available fields
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'type' => $validated['type'],
                'mobile' => $validated['mobile'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'address_line1' => $validated['address_line1'] ?? null,
                'address_line2' => $validated['address_line2'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'website' => $validated['website'] ?? null,
                'social_links' => $validated['social_links'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ];
            
            // Only update password if provided
            if (isset($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }
            
            $user->update($updateData);
            
            // Handle roles if any
            if (isset($validated['roles'])) {
                // Detach all current roles for this business
                $user->roles()->wherePivot('business_id', $current_business_id)->detach();
                
                // Attach new roles
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
