<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPackagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('memberships.view') || $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MembershipPackage $membershipPackage): bool
    {
        return $user->hasPermissionTo('memberships.view') || $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('memberships.create') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MembershipPackage $membershipPackage): bool
    {
        return $user->hasPermissionTo('memberships.update') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MembershipPackage $membershipPackage): bool
    {
        return $user->hasPermissionTo('memberships.delete') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MembershipPackage $membershipPackage): bool
    {
        return $user->hasPermissionTo('memberships.restore') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MembershipPackage $membershipPackage): bool
    {
        return $user->hasPermissionTo('memberships.force-delete') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can upload image.
     */
    public function uploadImage(User $user, MembershipPackage $membershipPackage): bool
    {
        return $user->hasPermissionTo('memberships.upload-image') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage pricing.
     */
    public function managePricing(User $user): bool
    {
        return $user->hasPermissionTo('memberships.manage-pricing') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage features.
     */
    public function manageFeatures(User $user): bool
    {
        return $user->hasPermissionTo('memberships.manage-features') || $user->hasRole('admin');
    }
}
