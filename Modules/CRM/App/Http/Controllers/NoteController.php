<?php

namespace Modules\CRM\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\CRM\App\Models\Note;
use Modules\CRM\App\Models\Contact;
use Modules\CRM\App\Models\Company;
use Modules\CRM\App\Models\Deal;
use Modules\CRM\App\Http\Requests\NoteRequest;
use App\Models\User;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Note::query()
            ->with(['user'])
            ->withCount([]);

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Handle filters
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by related entity type
        if ($request->has('notable_type') && $request->notable_type) {
            $query->where('notable_type', $request->notable_type);
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate the results
        $notes = $query->paginate($request->input('per_page', 10))
            ->withQueryString();

        $users = User::select('id', 'name')->get();

        $notableTypes = [
            'Modules\\CRM\\App\\Models\\Contact' => 'Contact',
            'Modules\\CRM\\App\\Models\\Company' => 'Company',
            'Modules\\CRM\\App\\Models\\Deal' => 'Deal',
        ];

        return Inertia::module('crm/notes/Index', [
            'notes' => $notes,
            'filters' => $request->only(['search', 'user_id', 'start_date', 'end_date', 'notable_type', 'sort_field', 'sort_direction', 'per_page']),
            'users' => $users,
            'notableTypes' => $notableTypes,
            'pageTitle' => 'Notes',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $contacts = Contact::select('id', 'first_name', 'last_name')->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->first_name . ' ' . $contact->last_name,
                ];
            });
            
        $companies = Company::select('id', 'name')->get();
        $deals = Deal::select('id', 'title')->get();
        $users = User::select('id', 'name')->get();
        
        // Get related entity if provided
        $relatedEntity = null;
        $relatedType = $request->input('related_type');
        $relatedId = $request->input('related_id');
        
        if ($relatedType && $relatedId) {
            switch ($relatedType) {
                case 'contact':
                    $relatedEntity = Contact::find($relatedId);
                    break;
                case 'company':
                    $relatedEntity = Company::find($relatedId);
                    break;
                case 'deal':
                    $relatedEntity = Deal::find($relatedId);
                    break;
            }
        }
        
        return Inertia::module('crm/notes/Create', [
            'contacts' => $contacts,
            'companies' => $companies,
            'deals' => $deals,
            'users' => $users,
            'relatedEntity' => $relatedEntity,
            'relatedType' => $relatedType,
            'pageTitle' => 'Create Note',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NoteRequest $request)
    {
        $note = Note::create($request->validated());
        
        // Associate with related entity if provided
        if ($request->has('notable_type') && $request->has('notable_id')) {
            $note->notable_type = $request->notable_type;
            $note->notable_id = $request->notable_id;
            $note->save();
        }

        return redirect()->route('crm.notes.index')
            ->with('success', 'Note created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        $note->load(['user', 'notable']);

        return Inertia::module('crm/notes/Show', [
            'note' => $note,
            'pageTitle' => $note->title,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        $contacts = Contact::select('id', 'first_name', 'last_name')->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->first_name . ' ' . $contact->last_name,
                ];
            });
            
        $companies = Company::select('id', 'name')->get();
        $deals = Deal::select('id', 'title')->get();
        $users = User::select('id', 'name')->get();
        
        return Inertia::module('crm/notes/Edit', [
            'note' => $note->load(['user', 'notable']),
            'contacts' => $contacts,
            'companies' => $companies,
            'deals' => $deals,
            'users' => $users,
            'pageTitle' => 'Edit ' . $note->title,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NoteRequest $request, Note $note)
    {
        $note->update($request->validated());
        
        // Update related entity if provided
        if ($request->has('notable_type') && $request->has('notable_id')) {
            $note->notable_type = $request->notable_type;
            $note->notable_id = $request->notable_id;
            $note->save();
        }

        return redirect()->route('crm.notes.show', $note)
            ->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return redirect()->route('crm.notes.index')
            ->with('success', 'Note deleted successfully.');
    }
}
