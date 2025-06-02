<?php

namespace Modules\CRM\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\CRM\App\Models\Deal;
use Modules\CRM\App\Models\Contact;
use Modules\CRM\App\Models\Company;
use Modules\CRM\App\Http\Requests\DealRequest;
use App\Models\User;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Deal::query()
            ->with(['assignedUser', 'contact', 'company'])
            ->withCount(['activities', 'tasks', 'notes']);

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('stage', 'like', "%{$searchTerm}%");
            });
        }

        // Handle filters
        if ($request->has('stage') && $request->stage) {
            $query->where('stage', $request->stage);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate the results
        $deals = $query->paginate($request->input('per_page', 10))
            ->withQueryString();

        // Get filter options
        $stages = [
            'lead' => 'Lead',
            'qualification' => 'Qualification',
            'proposal' => 'Proposal',
            'negotiation' => 'Negotiation',
            'closed_won' => 'Closed Won',
            'closed_lost' => 'Closed Lost',
        ];

        $statuses = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $users = getCurrentBusinessUsers();

        return Inertia::module('crm/deals/Index', [
            'deals' => $deals,
            'filters' => $request->only(['search', 'stage', 'status', 'user_id', 'sort_field', 'sort_direction', 'per_page']),
            'stages' => $stages,
            'statuses' => $statuses,
            'users' => $users,
            'pageTitle' => 'Deals',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contacts = Contact::select('id', 'first_name', 'last_name', 'email')->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->first_name . ' ' . $contact->last_name,
                    'email' => $contact->email,
                ];
            });
            
        $companies = Company::select('id', 'name')->get();
        $users = getCurrentBusinessUsers();

        $stages = [
            'lead' => 'Lead',
            'qualification' => 'Qualification',
            'proposal' => 'Proposal',
            'negotiation' => 'Negotiation',
            'closed_won' => 'Closed Won',
            'closed_lost' => 'Closed Lost',
        ];

        $statuses = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        return Inertia::module('crm/deals/Create', [
            'contacts' => $contacts,
            'companies' => $companies,
            'users' => $users,
            'stages' => $stages,
            'statuses' => $statuses,
            'pageTitle' => 'Create Deal',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DealRequest $request)
    {
        $deal = Deal::create($request->validated());

        return redirect()->route('crm.deals.index')
            ->with('success', 'Deal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Deal $deal)
    {
        $deal->load(['assignedUser', 'contact', 'company', 'activities', 'tasks', 'notes']);

        return Inertia::module('crm/deals/Show', [
            'deal' => $deal,
            'pageTitle' => $deal->title,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deal $deal)
    {
        $contacts = Contact::select('id', 'first_name', 'last_name', 'email')->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->first_name . ' ' . $contact->last_name,
                    'email' => $contact->email,
                ];
            });
            
        $companies = Company::select('id', 'name')->get();
        $users = getCurrentBusinessUsers();

        $stages = [
            'lead' => 'Lead',
            'qualification' => 'Qualification',
            'proposal' => 'Proposal',
            'negotiation' => 'Negotiation',
            'closed_won' => 'Closed Won',
            'closed_lost' => 'Closed Lost',
        ];

        $statuses = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        return Inertia::module('crm/deals/Edit', [
            'deal' => $deal,
            'contacts' => $contacts,
            'companies' => $companies,
            'users' => $users,
            'stages' => $stages,
            'statuses' => $statuses,
            'pageTitle' => 'Edit ' . $deal->title,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DealRequest $request, Deal $deal)
    {
        $deal->update($request->validated());

        return redirect()->route('crm.deals.show', $deal)
            ->with('success', 'Deal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal)
    {
        $deal->delete();

        return redirect()->route('crm.deals.index')
            ->with('success', 'Deal deleted successfully.');
    }
}
