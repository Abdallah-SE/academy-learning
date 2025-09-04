<?php

namespace App\Policies;

use App\Models\User;
use Modules\Admin\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any admins.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('admins.view') || $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can view the admin.
     */
    public function view(User $user, Admin $admin): bool
    {
        return $user->hasPermissionTo('admins.view') || $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can create admins.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('admins.create') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can update the admin.
     */
    public function update(User $user, Admin $admin): bool
    {
        // Users can update their own admin profile
        if ($user->id === $admin->id) {
            return true;
        }

        return $user->hasPermissionTo('admins.edit') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can delete the admin.
     */
    public function delete(User $user, Admin $admin): bool
    {
        // Prevent admin from deleting themselves
        if ($user->id === $admin->id) {
            return false;
        }

        return $user->hasPermissionTo('admins.delete') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can restore the admin.
     */
    public function restore(User $user, Admin $admin): bool
    {
        return $user->hasPermissionTo('admins.restore') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the admin.
     */
    public function forceDelete(User $user, Admin $admin): bool
    {
        return $user->hasPermissionTo('admins.force_delete') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can manage admin roles.
     */
    public function manageRoles(User $user, Admin $admin): bool
    {
        return $user->hasPermissionTo('admins.manage_roles') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can manage admin permissions.
     */
    public function managePermissions(User $user, Admin $admin): bool
    {
        return $user->hasPermissionTo('admins.manage_permissions') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can access admin dashboard.
     */
    public function accessDashboard(User $user): bool
    {
        return $user->hasPermissionTo('admin.dashboard.access') || $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can manage system settings.
     */
    public function manageSystemSettings(User $user): bool
    {
        return $user->hasPermissionTo('system.settings.manage') || $user->hasRole('super_admin');
    }
}
