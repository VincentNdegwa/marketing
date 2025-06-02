<?php

namespace Modules\CRM\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\CRM\App\Models\Activity;
use Modules\CRM\App\Models\Contact;
use Modules\CRM\App\Models\Company;
use Modules\CRM\App\Models\Deal;
use Modules\CRM\App\Http\Requests\ActivityRequest;
use Modules\UserManagement\App\Models\User;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Activity::query()
            ->with(['creator', 'assignee'])
            ->withCount([]);

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('type', 'like', "%{$searchTerm}%");
            });
        }

        // Handle filters
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('creator_id') && $request->creator_id) {
            $query->where('creator_id', $request->creator_id);
        }

        if ($request->has('assignee_id') && $request->assignee_id) {
            $query->where('assignee_id', $request->assignee_id);
        }

        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('due_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('due_date', '<=', $request->end_date);
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'due_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate the results
        $activities = $query->paginate($request->input('per_page', 10))
            ->withQueryString();

        // Get filter options
        $types = [
            'call' => 'Call',
            'meeting' => 'Meeting',
            'email' => 'Email',
            'task' => 'Task',
            'deadline' => 'Deadline',
            'other' => 'Other',
        ];

        $statuses = [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $users = User::select('id', 'name')->get();

        return Inertia::module('crm/activities/Index', [
            'activities' => $activities,
            'filters' => $request->only(['search', 'type', 'status', 'creator_id', 'assignee_id', 'start_date', 'end_date', 'sort_field', 'sort_direction', 'per_page']),
            'types' => $types,
            'statuses' => $statuses,
            'users' => $users,
            'pageTitle' => 'Activities',
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
        
        $types = [
            'call' => 'Call',
            'meeting' => 'Meeting',
            'email' => 'Email',
            'task' => 'Task',
            'deadline' => 'Deadline',
            'other' => 'Other',
        ];

        $statuses = [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        // Handle related entity if provided
        $relatedType = $request->input('related_type');
        $relatedId = $request->input('related_id');
        $relatedEntity = null;
        
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
        
        return Inertia::module('crm/activities/Create', [
            'contacts' => $contacts,
            'companies' => $companies,
            'deals' => $deals,
            'users' => $users,
            'types' => $types,
            'statuses' => $statuses,
            'relatedEntity' => $relatedEntity,
            'relatedType' => $relatedType,
            'pageTitle' => 'Create Activity',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActivityRequest $request)
    {
        $activity = Activity::create($request->validated());
        
        // Associate with related entity if provided
        if ($request->has('activitable_type') && $request->has('activitable_id')) {
            $activity->activitable_type = $request->activitable_type;
            $activity->activitable_id = $request->activitable_id;
            $activity->save();
        }

        return redirect()->route('crm.activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $activity->load(['creator', 'assignee', 'activitable']);

        return Inertia::module('crm/activities/Show', [
            'activity' => $activity,
            'pageTitle' => $activity->subject,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
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
        
        $types = [
            'call' => 'Call',
            'meeting' => 'Meeting',
            'email' => 'Email',
            'task' => 'Task',
            'deadline' => 'Deadline',
            'other' => 'Other',
        ];

        $statuses = [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        return Inertia::module('crm/activities/Edit', [
            'activity' => $activity->load(['creator', 'assignee', 'activitable']),
            'contacts' => $contacts,
            'companies' => $companies,
            'deals' => $deals,
            'users' => $users,
            'types' => $types,
            'statuses' => $statuses,
            'pageTitle' => 'Edit ' . $activity->subject,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $activity->update($request->validated());
        
        // Update related entity if provided
        if ($request->has('activitable_type') && $request->has('activitable_id')) {
            $activity->activitable_type = $request->activitable_type;
            $activity->activitable_id = $request->activitable_id;
            $activity->save();
        }

        return redirect()->route('crm.activities.show', $activity)
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('crm.activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
