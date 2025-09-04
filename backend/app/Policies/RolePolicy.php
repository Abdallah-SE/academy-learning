<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Check if user is trying to edit a higher level role
        if ($role->level >= $user->getHighestRoleLevel()) {
            return false;
        }

        return $user->hasPermissionTo('roles.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Check if user is trying to delete a higher level role
        if ($role->level >= $user->getHighestRoleLevel()) {
            return false;
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            return false;
        }

        // Check if role is a system role
        if (in_array($role->name, ['student', 'teacher', 'admin', 'super_admin'])) {
            return false;
        }

        return $user->hasPermissionTo('roles.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        // Check if user is trying to delete a higher level role
        if ($role->level >= $user->getHighestRoleLevel()) {
            return false;
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            return false;
        }

        // Check if role is a system role
        if (in_array($role->name, ['student', 'teacher', 'admin', 'super_admin'])) {
            return false;
        }

        return $user->hasPermissionTo('roles.delete');
    }

    /**
     * Determine whether the user can assign permissions to the role.
     */
    public function assignPermissions(User $user, Role $role): bool
    {
        // Check if user is trying to edit a higher level role
        if ($role->level >= $user->getHighestRoleLevel()) {
            return false;
        }

        return $user->hasPermissionTo('roles.edit');
    }

    /**
     * Determine whether the user can view role statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can duplicate the role.
     */
    public function duplicate(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    /**
     * Determine whether the user can manage role levels.
     */
    public function manageLevels(User $user): bool
    {
        // Only super admins can manage role levels
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can assign roles to users.
     */
    public function assignToUsers(User $user): bool
    {
        return $user->hasPermissionTo('users.manage_roles');
    }

    /**
     * Determine whether the user can view role permissions.
     */
    public function viewPermissions(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can manage role hierarchy.
     */
    public function manageHierarchy(User $user): bool
    {
        // Only super admins can manage role hierarchy
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view role assignments.
     */
    public function viewAssignments(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can manage role restrictions.
     */
    public function manageRestrictions(User $user, Role $role): bool
    {
        // Check if user is trying to edit a higher level role
        if ($role->level >= $user->getHighestRoleLevel()) {
            return false;
        }

        return $user->hasPermissionTo('roles.edit');
    }

    /**
     * Determine whether the user can view role analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can export role data.
     */
    public function exportData(User $user): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can manage role templates.
     */
    public function manageTemplates(User $user): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    /**
     * Determine whether the user can view role history.
     */
    public function viewHistory(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    /**
     * Determine whether the user can manage role inheritance.
     */
    public function manageInheritance(User $user): bool
    {
        // Only super admins can manage role inheritance
        return $user->isSuperAdmin();
    }
}
