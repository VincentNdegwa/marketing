<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Modules\Business\app\Models\Business;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->with([
                'businesses.users' => function ($query) {
                    $query->orderBy('name');
                },
                'roles' => function ($query) {
                    $query->where('name', 'admin');
                },
            ])
            ->latest()
            ->paginate(10);

        $adminUsers->through(function ($user) {
            $user->businesses = $user->businesses->map(function ($business) {
                $business->has_roles = count($business->roles) > 0;
                $business->users = $business->users->map(function ($user) use ($business) {
                    $user->roles = $user->rolesForBusiness($business->id);

                    return $user;
                });

                return $business;
            });
            $adminBusinesses = $user->businesses->filter(function ($business) use ($user) {
                return $user->isAdminForBusiness($business->id);
            })->values();
            $user->admin_businesses = $adminBusinesses;
            $user->default_business_id = $user->defaultBusiness()?->id;
            $user->user_type = 'Business User';
            $user->roles = $user->roles();

            return $user;
        });

        return Inertia::render('admin/clients/Clients', [
            'users' => $adminUsers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Business validation
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:businesses,email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',

            // Admin User validation
            'admin_user.name' => 'required|string|max:255',
            'admin_user.email' => 'required|email|unique:users,email',
            'admin_user.password' => 'required|string|min:8',
        ]);

        DB::beginTransaction();

        try {
            $admin = User::create([
                'name' => $validated['admin_user']['name'],
                'email' => $validated['admin_user']['email'],
                'password' => Hash::make($validated['admin_user']['password']),
                'type' => 'admin',
            ]);

            // Create the business and associate with admin user
            $business = Business::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'slug' => Str::slug($validated['name']).'-'.uniqid(),
                'phone' => $validated['phone'],
                'website' => $validated['website'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'zip_code' => $validated['zip_code'],
                'description' => $validated['description'] ?? '',
                'is_active' => $validated['is_active'] ?? true,
                'admin_user_id' => $admin->id,
            ]);

            Business::permitNewBusiness($business->id, $validated['admin_user']['name'], $validated['admin_user']['email']);

            DB::commit();

            return redirect()->back()->with('success', 'Business and admin user created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating business and admin user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the business and user. Please try again.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $adminUserId = data_get($request->input('admin_user'), 'id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('businesses')->ignore($id)],
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',

            // Admin user validations
            'admin_user.id' => 'required|string|exists:users,id',
            'admin_user.name' => 'required|string|max:255',
            'admin_user.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($adminUserId),
            ],
            'admin_user.password' => 'nullable|min:8',
        ]);

        DB::beginTransaction();

        try {
            $business = Business::findOrFail($id);

            $admin = User::findOrFail($validated['admin_user']['id']);

            $adminUpdate = [
                'name' => $validated['admin_user']['name'],
                'email' => $validated['admin_user']['email'],
            ];

            if (! empty($validated['admin_user']['password'])) {
                $adminUpdate['password'] = Hash::make($validated['admin_user']['password']);
            }

            $admin->update($adminUpdate);

            $business->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']).'-'.uniqid(),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'website' => $validated['website'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'zip_code' => $validated['zip_code'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Business and admin user updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating business and admin user', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while updating. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('user_type', 'Admin')->findOrFail($id);

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin users cannot be deleted');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Admin user deleted successfully');
    }
}
