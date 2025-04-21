<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

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
                'businesses' => function ($query) {
                    $query->orderBy('name');
                },
                'roles' => function ($query) {
                    $query->where('name', 'admin');
                }
            ])
            ->latest()
            ->paginate(10);

        $adminUsers->through(function ($user) {
            $adminBusinesses = $user->businesses->filter(function ($business) use ($user) {
                return $user->isAdminForBusiness($business->id);
            })->values();
            
            $user->admin_businesses = $adminBusinesses;
            $user->default_business_id = $user->defaultBusiness()?->id;
            $user->user_type = 'Business User';
            $user->roles =$user->roles();
            
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'user_type' => 'Admin',
        ]);

        return redirect()->back()->with('success', 'Admin user created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::where('user_type', 'Admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|min:8',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Admin user updated successfully');
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
