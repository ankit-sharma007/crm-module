<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Requests\UpdateLeadStatusRequest;
use App\Http\Requests\AddLeadNoteRequest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'store', 'update', 'destroy', 'assign', 'updateStatus', 'addNote']);
        $this->middleware('auth:sanctum')->only(['index', 'show', 'store', 'update', 'destroy', 'assign', 'updateStatus', 'addNote']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);
        $leads = Lead::with('assignedTo')->paginate(10); // Paginate 10 per page

        if ($request->expectsJson()) {
            return response()->json(['leads' => $leads]);
        }
        return view('leads.index', compact('leads'));
    }

    public function myLeads()
    {
        $leads = Lead::where('assigned_to', Auth::id())->with('assignedTo')->paginate(10);
        return view('leads.my-leads', compact('leads'));
    }

    public function create()
    {
        $this->authorize('create', Lead::class);
        $agents = User::where('is_admin', false)->get();
        return view('leads.create', compact('agents'));
    }

    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();
        $lead = Lead::create($data);

        $lead->activities()->create([
            'user_id' => Auth::id(),
            'action' => \App\Enums\LeadActivityAction::CREATED->value,
            'notes' => 'Lead created',
        ]);

        if ($request->filled('assigned_to')) {
            $lead->assignedTo()->associate($request->assigned_to)->save();
            $lead->activities()->create([
                'user_id' => Auth::id(),
                'action' => \App\Enums\LeadActivityAction::ASSIGNED->value,
                'notes' => 'Lead assigned to ' . User::find($request->assigned_to)->name,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Lead created successfully', 'lead' => $lead], 201);
        }
        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead, Request $request)
    {
        $this->authorize('view', $lead);
        $lead->load('assignedTo', 'activities.user');

        if ($request->expectsJson()) {
            return response()->json(['lead' => $lead]);
        }
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $this->authorize('update', $lead);
        $agents = User::where('is_admin', false)->get();
        return view('leads.edit', compact('lead', 'agents'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $data = $request->validated();
        $originalStatus = $lead->status;

        $lead->update($data);

        if ($request->filled('assigned_to') && $lead->assigned_to != $request->assigned_to) {
            $lead->assignedTo()->associate($request->assigned_to)->save();
            $lead->activities()->create([
                'user_id' => Auth::id(),
                'action' => \App\Enums\LeadActivityAction::ASSIGNED->value,
                'notes' => 'Lead assigned to ' . User::find($request->assigned_to)->name,
            ]);
        }

        if ($originalStatus->value !== $data['status']) {
            $lead->activities()->create([
                'user_id' => Auth::id(),
                'action' => \App\Enums\LeadActivityAction::STATUS_UPDATED->value,
                'notes' => "Status changed from {$originalStatus->value} to {$data['status']}",
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Lead updated successfully', 'lead' => $lead]);
        }
        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead, Request $request)
    {
        $this->authorize('delete', $lead);
        $lead->activities()->create([
            'user_id' => Auth::id(),
            'action' => \App\Enums\LeadActivityAction::DELETED->value,
            'notes' => 'Lead deleted',
        ]);
        $lead->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Lead deleted successfully'], 204);
        }
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function assign(Request $request, Lead $lead)
    {
        $this->authorize('assign', Lead::class);
        $request->validate(['assigned_to' => 'nullable|exists:users,id']);

        $lead->assignedTo()->associate($request->assigned_to)->save();
        $activity = $lead->activities()->create([
            'user_id' => Auth::id(),
            'action' => \App\Enums\LeadActivityAction::ASSIGNED->value,
            'notes' => $request->assigned_to ? 'Lead assigned to ' . User::find($request->assigned_to)->name : 'Lead unassigned',
        ])->load('user');

        return response()->json([
            'message' => 'Lead assigned successfully',
            'activity' => [
                'action' => ucfirst($activity->action->value),
                'user' => $activity->user->name,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                'notes' => $activity->notes ?? 'N/A',
            ]
        ]);
    }

    public function updateStatus(UpdateLeadStatusRequest $request, Lead $lead)
    {
        $this->authorize('updateStatus', $lead);
        $data = $request->validated();
        $originalStatus = $lead->status;

        $lead->update(['status' => $data['status']]);

        $activity = null;
        if ($originalStatus->value !== $data['status']) {
            $activity = $lead->activities()->create([
                'user_id' => Auth::id(),
                'action' => \App\Enums\LeadActivityAction::STATUS_UPDATED->value,
                'notes' => "Status changed from {$originalStatus->value} to {$data['status']}",
            ])->load('user');
        }

        return response()->json([
            'message' => 'Status updated successfully',
            'activity' => $activity ? [
                'action' => ucfirst($activity->action->value),
                'user' => $activity->user->name,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                'notes' => $activity->notes ?? 'N/A',
            ] : null
        ]);
    }

    public function addNote(AddLeadNoteRequest $request, Lead $lead)
    {
        $this->authorize('addNote', $lead);
        $data = $request->validated();

        $activity = $lead->activities()->create([
            'user_id' => Auth::id(),
            'action' => \App\Enums\LeadActivityAction::COMMENTED->value,
            'notes' => $data['notes'],
        ])->load('user');

        return response()->json([
            'message' => 'Note added successfully',
            'activity' => [
                'action' => ucfirst($activity->action->value),
                'user' => $activity->user->name,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                'notes' => $activity->notes ?? 'N/A',
            ]
        ]);
    }

    public function activities(Request $request)
    {
        $this->authorize('viewAny', Lead::class);
        $query = \App\Models\LeadActivity::with(['lead', 'user'])
            ->whereHas('lead')
            ->orderBy('created_at', 'desc');

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $activities = $query->paginate(10); // Paginate 10 per page
        $leads = Lead::all()->pluck('first_name', 'id');
        $users = User::all()->pluck('name', 'id');
        $actions = array_column(\App\Enums\LeadActivityAction::cases(), 'value');

        if ($request->expectsJson()) {
            return response()->json(['activities' => $activities]);
        }
        return view('leads.activities', compact('activities', 'leads', 'users', 'actions'));
    }
}
