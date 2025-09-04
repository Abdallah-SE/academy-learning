<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserMembership;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserMembershipPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('memberships.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserMembership $membership): bool
    {
        // Users can view their own memberships
        if ($user->id === $membership->user_id) {
            return true;
        }

        // Check if user has permission to view other memberships
        return $user->hasPermissionTo('memberships.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('memberships.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserMembership $membership): bool
    {
        // Users can update their own memberships (limited fields)
        if ($user->id === $membership->user_id) {
            // Users can only cancel their own memberships
            return true;
        }

        // Check if user has permission to edit other memberships
        return $user->hasPermissionTo('memberships.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserMembership $membership): bool
    {
        return $user->hasPermissionTo('memberships.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserMembership $membership): bool
    {
        return $user->hasPermissionTo('memberships.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserMembership $membership): bool
    {
        return $user->hasPermissionTo('memberships.delete');
    }

    /**
     * Determine whether the user can verify payment.
     */
    public function verifyPayment(User $user, UserMembership $membership): bool
    {
        return $user->hasPermissionTo('memberships.verify_payment');
    }

    /**
     * Determine whether the user can view membership statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasPermissionTo('memberships.view_statistics');
    }

    /**
     * Determine whether the user can subscribe to packages.
     */
    public function subscribe(User $user): bool
    {
        // Users can subscribe to packages if they don't have an active membership
        return !$user->hasActiveMembership();
    }

    /**
     * Determine whether the user can cancel membership.
     */
    public function cancel(User $user, UserMembership $membership): bool
    {
        // Users can only cancel their own memberships
        if ($user->id !== $membership->user_id) {
            return false;
        }

        // Users can only cancel active memberships
        return $membership->status === 'active';
    }

    /**
     * Determine whether the user can view their own membership.
     */
    public function viewOwn(User $user): bool
    {
        // Users can always view their own membership
        return true;
    }

    /**
     * Determine whether the user can view membership history.
     */
    public function viewHistory(User $user): bool
    {
        // Users can always view their own membership history
        return true;
    }

    /**
     * Determine whether the user can manage payment verification.
     */
    public function managePaymentVerification(User $user): bool
    {
        return $user->hasPermissionTo('memberships.verify_payment');
    }

    /**
     * Determine whether the user can view revenue statistics.
     */
    public function viewRevenueStats(User $user): bool
    {
        return $user->hasPermissionTo('memberships.view_statistics');
    }

    /**
     * Determine whether the user can export membership data.
     */
    public function exportData(User $user): bool
    {
        return $user->hasPermissionTo('memberships.view_statistics');
    }

    /**
     * Determine whether the user can manage membership status.
     */
    public function manageStatus(User $user, UserMembership $membership): bool
    {
        return $user->hasPermissionTo('memberships.edit');
    }

    /**
     * Determine whether the user can extend membership.
     */
    public function extendMembership(User $user, UserMembership $membership): bool
    {
        // Users can extend their own memberships
        if ($user->id === $membership->user_id) {
            return true;
        }

        // Admins can extend any membership
        return $user->hasPermissionTo('memberships.edit');
    }

    /**
     * Determine whether the user can view membership details.
     */
    public function viewDetails(User $user, UserMembership $membership): bool
    {
        // Users can view their own membership details
        if ($user->id === $membership->user_id) {
            return true;
        }

        // Check if user has permission to view other memberships
        return $user->hasPermissionTo('memberships.view');
    }

    /**
     * Determine whether the user can manage membership features.
     */
    public function manageFeatures(User $user, UserMembership $membership): bool
    {
        return $user->hasPermissionTo('memberships.edit');
    }

    /**
     * Determine whether the user can view membership analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasPermissionTo('memberships.view_statistics');
    }

    /**
     * Determine whether the user can manage membership restrictions.
     */
    public function manageRestrictions(User $user, UserMembership $membership): bool
    {
        return $user->hasPermissionTo('memberships.edit');
    }

    /**
     * Determine whether the user can view membership reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->hasPermissionTo('memberships.view_statistics');
    }
}
