<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Business\App\Models\Business;

class BusinessController extends Controller
{
    /**
     * Display a listing of the business
     */
    public function index()
    {
        $user = Auth::user();
        $businesses = $user->businesses;
        $current_business_id = getCurrentBusinessId();
        return Inertia::module('business/Index', [
            'businesses' => $businesses->map(function($busines)use($current_business_id, $user){
                if(!isset($current_business_id)){
                    $current_business_id = $user->defaultBusiness()?->id;
                }
                $busines->is_current = $busines->id === $current_business_id;
                return $busines;
            }),
            'defaultBusinessId' => $user->defaultBusiness()?->id,
        ]);
    }

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        return Inertia::module('business/Create');
    }

    /**
     * Store a newly created business in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        $business = Business::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'zip_code' => $request->zip_code,
            'website' => $request->website,
            'is_active' => true,
        ]);

        $user = Auth::user();

        // If this is the user's first business, make it the default
        $isDefault = $user->businesses()->count() === 0;

        $user->businesses()->attach($business, ['is_default' => $isDefault]);

        // Assign appropriate roles to the user for this business
        if ($user->isSuperAdmin()) {
            // Find the admin role for this business or create it
            $adminRole = \App\Models\Role::firstOrCreate(
                ['name' => 'admin', 'business_id' => $business->id],
                [
                    'display_name' => 'Administrator',
                    'description' => 'User with access to most system features',
                ]
            );

            $user->roles()->attach($adminRole, ['business_id' => $business->id]);
        }

        return redirect()->route('business.index')
            ->with('success', 'Business created successfully.');
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business)
    {
        // Check if user has access to this business
        $user = Auth::user();
        if (! $user->isSuperAdmin() && ! $user->businesses->contains($business->id)) {
            abort(403, 'Unauthorized access.');
        }

        return Inertia::module('business/Show', [
            'business' => $business,
        ]);
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(Business $business)
    {
        // Check if user has access to this business
        $user = Auth::user();
        if (! $user->isSuperAdmin() && ! $user->businesses->contains($business->id)) {
            abort(403, 'Unauthorized access.');
        }

        return Inertia::module('business/Edit', [
            'business' => $business,
        ]);
    }

    /**
     * Update the specified business in storage.
     */
    public function update(Request $request, Business $business)
    {
        // Check if user has access to this business
        $user = Auth::user();
        if (! $user->isSuperAdmin() && ! $user->businesses->contains($business->id)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);

        $business->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'zip_code' => $request->zip_code,
            'website' => $request->website,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('business.index')
            ->with('success', 'Business updated successfully.');
    }

    /**
     * Remove the specified business from storage.
     */
    public function destroy($id)
    {
        $business = Business::findOrFail($id);
        $user = Auth::user();
        if ($user->defaultBusiness() && $user->defaultBusiness()->id === $business->id) {
            $newDefault = $user->businesses()->where('id', '!=', $business->id)->first();
            if ($newDefault) {
                $user->setDefaultBusiness($newDefault->id);
            }
        }
        $business->users()->detach();
        $business->delete();
        return redirect()->route('business.index')
            ->with('success', 'Business deleted successfully.');
    }
    
    /**
     * Set a business as the default for the current user.
     */
    public function setAsDefault($id)
    {
        $business = Business::findOrFail($id);
        $user = Auth::user();
        if (!$user->businesses()->where('business_id', $business->id)->exists()) {
            return redirect()->back()->with('error', 'You do not have access to this business.');
        }
        $user->setDefaultBusiness($business->id);
        return redirect()->back()->with('success', 'Default business updated successfully.');
    }
    
    /**
     * Switch to a different business for the current session.
     */
    public function switchBusiness($id)
    {
        $business = Business::findOrFail($id);
        $user = Auth::user();        
        if (!$user->businesses()->where('business_id', $business->id)->exists()) {
            return redirect()->back()->with('error', 'You do not have access to this business.');
        }
        setCurrentBusiness($business->id);
        return redirect()->route('dashboard')
            ->with('success', 'Switched to ' . $business->name);    }

    /**
     * Set the specified business as the default for the authenticated user.
     */
    public function setDefault(Business $business)
    {
        $user = Auth::user();

        if (! $user->businesses->contains($business->id)) {
            abort(403, 'Unauthorized access.');
        }

        $user->setDefaultBusiness($business->id);

        setCurrentBusiness($business->id);

        return redirect()->back()
            ->with('success', 'Default business updated successfully.');
    }
}
