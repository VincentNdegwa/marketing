<?php

namespace Modules\CRM\App\Http\Controllers;

use App\Http\Controllers\Controller;
use FacebookAds\Object\BusinessUser;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\CRM\App\Models\Contact;
use Modules\CRM\App\Models\Company;
use Modules\CRM\App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Business\App\Models\Business;

class ContactController extends Controller
{
    /**
     * Display a listing of the contacts.
     */
    public function index(Request $request)
    {
        $contacts = Contact::with('company', 'assignedUser')
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->company_id, function($query, $companyId) {
                $query->where('company_id', $companyId);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->lead_status, function($query, $leadStatus) {
                $query->where('lead_status', $leadStatus);
            })
            ->when($request->assigned_to, function($query, $assignedTo) {
                $query->where('assigned_to', $assignedTo);
            })
            ->orderBy($request->sort_field ?? 'created_at', $request->sort_direction ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

            $users = getCurrentBusinessUsers();

        return Inertia::module('crm/Contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['search', 'status', 'lead_status', 'company_id', 'assigned_to', 'sort_field', 'sort_direction', 'per_page']),
            'pageTitle' => 'Contacts',
            'companies' => Company::select('id', 'name')->orderBy('name')->get(),
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create(Request $request)
    {
        $company_id = $request->query('company_id');
        
        $companies = Company::orderBy('name');
        if ($company_id) {
            $companies = $companies->where('id', $company_id);
        }
        $companies = $companies->get(['id', 'name']);
        $users = getCurrentBusinessUsers();
    
        return Inertia::module('crm/Contacts/Create', [
            'companies' => $companies,
            'pageTitle' => 'Create Contact',
            'users' => $users,
            'company_id' => $company_id,
        ]);
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(ContactRequest $request)
    {
        $data = $request->validated();
        $data['assigned_to'] = $data['assigned_to'] ?? Auth::id();
        
        $contact = Contact::create($data);

        return redirect()->route('crm.contacts.show', $contact->id)
            ->with('success', 'Contact created successfully.');
    }

    /**
     * Display the specified contact.
     */
    public function show(Contact $contact)
    {
        $contact->load(['company', 'assignedUser', 'activities', 'deals', 'tasks', 'notes']);
        
        return Inertia::module('crm/Contacts/Show', [
            'contact' => $contact,
            'pageTitle' => $contact->full_name,
        ]);
    }

    /**
     * Show the form for editing the specified contact.
     */
    public function edit(Contact $contact)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $users = getCurrentBusinessUsers();

        return Inertia::module('crm/Contacts/Edit', [
            'contact' => $contact,
            'companies' => $companies,
            'pageTitle' => 'Edit ' . $contact->full_name,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(ContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());

        return redirect()->route('crm.contacts.show', $contact->id)
            ->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('crm.contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }
}
