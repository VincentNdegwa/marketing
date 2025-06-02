<?php

namespace Modules\CRM\App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\CRM\App\Models\Company;
use App\Http\Controllers\Controller;
use Modules\Business\App\Models\Business;
use Modules\CRM\App\Http\Requests\CompanyRequest;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::query()
            ->with(['assignedUser', 'contacts'])
            ->withCount(['contacts', 'deals']);

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('website', 'like', "%{$searchTerm}%");
            });
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate the results
        $companies = $query->paginate($request->input('per_page', 10))
            ->withQueryString();


        return Inertia::module('crm/companies/Index', [
            'companies' => $companies,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction', 'per_page']),
            'pageTitle' => 'Companies',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::select('id', 'name')->get();
        
        return Inertia::module('crm/companies/Create', [
            'users' => $users,
            'pageTitle' => 'Create Company',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return redirect()->route('crm.companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load(['assignedUser', 'contacts', 'deals', 'activities', 'tasks', 'notes']);

        return Inertia::module('crm/companies/Show', [
            'company' => $company,
            'pageTitle' => $company->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $users = User::select('id', 'name')->get();
        
        return Inertia::module('crm/companies/Edit', [
            'company' => $company,
            'users' => $users,
            'pageTitle' => 'Edit ' . $company->name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return redirect()->route('crm.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('crm.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
