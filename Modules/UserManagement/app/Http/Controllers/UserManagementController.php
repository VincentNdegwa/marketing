<?php

namespace Modules\UserManagement\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        return Inertia::module('usermanagement/users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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
        return Inertia::module('usermanagement/users/Edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
