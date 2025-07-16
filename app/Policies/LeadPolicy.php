<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class LeadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        Log::debug('LeadPolicy::viewAny', [
            'user_id' => $user->id,
            'is_admin' => $user->is_admin,
            'can_view_any' => $user->is_admin || !$user->is_admin,
        ]);
        return true; // Admins and agents (non-admins) can view the leads index and dashboard
    }

    public function view(User $user, Lead $lead)
    {
        return $user->is_admin || $lead->assigned_to === $user->id;
    }

    public function create(User $user)
    {
        return $user->is_admin;
    }

    public function update(User $user, Lead $lead)
    {
        $canUpdate = $user->is_admin || $lead->assigned_to === $user->id;
        Log::debug('LeadPolicy::update', [
            'user_id' => $user->id,
            'is_admin' => $user->is_admin,
            'lead_id' => $lead->id,
            'assigned_to' => $lead->assigned_to,
            'can_update' => $canUpdate,
        ]);
        return $canUpdate;
    }

    public function delete(User $user, Lead $lead)
    {
        return $user->is_admin;
    }

    public function assign(User $user)
    {
        return $user->is_admin;
    }

    public function updateStatus(User $user, Lead $lead)
    {
        return $user->is_admin || $lead->assigned_to === $user->id;
    }

    public function addNote(User $user, Lead $lead)
    {
        return $user->is_admin || $lead->assigned_to === $user->id;
    }
}
