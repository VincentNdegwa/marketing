<?php

namespace Modules\CRM\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\CRM\App\Models\Contact;
use Modules\CRM\App\Models\Company;
use Modules\CRM\App\Models\Deal;
use Modules\CRM\App\Models\Activity;
use Modules\CRM\App\Models\Task;

class CRMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get statistics for the dashboard
        $stats = [
            'contacts' => Contact::count(),
            'companies' => Company::count(),
            'openDeals' => Deal::whereIn('status', ['open', 'in_progress', 'negotiation'])->count(),
            'pendingTasks' => Task::where('status', 'pending')->count(),
        ];

        // Get recent activities (limit to 5)
        $recentActivities = Activity::with(['creator', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'subject' => $activity->subject,
                    'description' => $activity->description,
                    'due_date' => $activity->due_date,
                    'status' => $activity->status,
                    'creator' => $activity->creator ? [
                        'id' => $activity->creator->id,
                        'name' => $activity->creator->name,
                    ] : null,
                    'assignee' => $activity->assignee ? [
                        'id' => $activity->assignee->id,
                        'name' => $activity->assignee->name,
                    ] : null,
                ];
            });

        // Get pipeline summary
        $dealStages = [
            'lead' => 'Lead',
            'qualification' => 'Qualification',
            'proposal' => 'Proposal',
            'negotiation' => 'Negotiation',
            'closed_won' => 'Closed Won',
            'closed_lost' => 'Closed Lost',
        ];

        $pipeline = [];
        $totalValue = Deal::sum('value');
        
        foreach ($dealStages as $stage => $name) {
            $dealsInStage = Deal::where('stage', $stage)->get();
            $stageValue = $dealsInStage->sum('value');
            $stageCount = $dealsInStage->count();
            
            $pipeline[] = [
                'name' => $name,
                'value' => $stageValue,
                'count' => $stageCount,
                'percentage' => $totalValue > 0 ? round(($stageValue / $totalValue) * 100, 1) : 0,
            ];
        }

        return Inertia::render('CRM/Dashboard/Index', [
            'pageTitle' => 'CRM Dashboard',
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'pipeline' => $pipeline,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/Create');
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
        return Inertia::render('CRM/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return Inertia::render('CRM/Edit');
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