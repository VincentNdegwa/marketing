<?php

namespace Modules\CRM\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\CRM\App\Models\Task;
use Modules\CRM\App\Models\Contact;
use Modules\CRM\App\Models\Company;
use Modules\CRM\App\Models\Deal;
use Modules\CRM\App\Http\Requests\TaskRequest;
use App\Models\User;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query()
            ->with(['creator', 'assignee'])
            ->withCount([]);

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('priority', 'like', "%{$searchTerm}%");
            });
        }

        // Handle filters
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
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
        $tasks = $query->paginate($request->input('per_page', 10))
            ->withQueryString();

        // Get filter options
        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

        $statuses = [
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'deferred' => 'Deferred',
        ];

        $users = User::select('id', 'name')->get();

        return Inertia::module('crm/tasks/index', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'priority', 'status', 'creator_id', 'assignee_id', 'start_date', 'end_date', 'sort_field', 'sort_direction', 'per_page']),
            'priorities' => $priorities,
            'statuses' => $statuses,
            'users' => $users,
            'pageTitle' => 'Tasks',
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
        
        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

        $statuses = [
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'deferred' => 'Deferred',
        ];
        
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
        
        return Inertia::module('crm/tasks/create', [
            'contacts' => $contacts,
            'companies' => $companies,
            'deals' => $deals,
            'users' => $users,
            'priorities' => $priorities,
            'statuses' => $statuses,
            'relatedEntity' => $relatedEntity,
            'relatedType' => $relatedType,
            'pageTitle' => 'Create Task',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $task = Task::create($request->validated());
        
        // Associate with related entity if provided
        if ($request->has('taskable_type') && $request->has('taskable_id')) {
            $task->taskable_type = $request->taskable_type;
            $task->taskable_id = $request->taskable_id;
            $task->save();
        }

        return redirect()->route('crm.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['creator', 'assignee', 'taskable']);

        return Inertia::module('crm/tasks/show', [
            'task' => $task,
            'pageTitle' => $task->title,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
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
        
        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

        $statuses = [
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'deferred' => 'Deferred',
        ];
        
        return Inertia::module('crm/tasks/edit', [
            'task' => $task->load(['creator', 'assignee', 'taskable']),
            'contacts' => $contacts,
            'companies' => $companies,
            'deals' => $deals,
            'users' => $users,
            'priorities' => $priorities,
            'statuses' => $statuses,
            'pageTitle' => 'Edit ' . $task->title,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        
        // Update related entity if provided
        if ($request->has('taskable_type') && $request->has('taskable_id')) {
            $task->taskable_type = $request->taskable_type;
            $task->taskable_id = $request->taskable_id;
            $task->save();
        }

        return redirect()->route('crm.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('crm.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
