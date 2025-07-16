<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Lead::class);
        $leads = Lead::with('assignedTo')->get();
        return view('leads.index', compact('leads'));
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

        if ($request->filled('assigned_to')) {
            $lead->assignedTo()->associate($request->assigned_to)->save();
            $lead->activities()->create([
                'user_id' => Auth::id(),
                'action' => \App\Enums\LeadActivityAction::ASSIGNED,
                'notes' => 'Lead assigned to ' . User::find($request->assigned_to)->name,
            ]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);
        $lead->load('assignedTo', 'activities.user');
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
                'action' => \App\Enums\LeadActivityAction::ASSIGNED,
                'notes' => 'Lead assigned to ' . User::find($request->assigned_to)->name,
            ]);
        }

        if ($originalStatus !== $data['status']) {
            $lead->activities()->create([
                'user_id' => Auth::id(),
                'action' => \App\Enums\LeadActivityAction::STATUS_UPDATED,
                'notes' => "Status changed from {$originalStatus->value} to {$data['status']}",
            ]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function assign(Request $request, Lead $lead)
    {
        $this->authorize('assign', Lead::class);
        $request->validate(['assigned_to' => 'nullable|exists:users,id']);

        $lead->assignedTo()->associate($request->assigned_to)->save();
        $lead->activities()->create([
            'user_id' => Auth::id(),
            'action' => \App\Enums\LeadActivityAction::ASSIGNED,
            'notes' => $request->assigned_to ? 'Lead assigned to ' . User::find($request->assigned_to)->name : 'Lead unassigned',
        ]);

        return response()->json(['message' => 'Lead assigned successfully']);
    }
}
