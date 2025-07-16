<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;

    // Admins can bypass all checks
    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_admin) {
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lead $lead): bool
    {
        return $user->is_admin || $lead->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lead $lead): bool
    {
        return $user->is_admin || $lead->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lead $lead): bool
    {
        return $user->is_admin;
    }

        // Assign/unassign leads (admin only)
    public function assign(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lead $lead): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lead $lead): bool
    {
        //
    }
        // Update lead status (admin or assigned sales agent)
    public function updateStatus(User $user, Lead $lead): bool
    {
        return $user->is_admin || $lead->assigned_to === $user->id;
    }

    // Add notes (admin or assigned sales agent)
    public function addNote(User $user, Lead $lead): bool
    {
        return $user->is_admin || $lead->assigned_to === $user->id;
    }
}
