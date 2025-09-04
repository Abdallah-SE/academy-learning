<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('permissions.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        // Check if permission is a system permission
        if (in_array($permission->name, [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'
        ])) {
            // Only super admins can edit system permissions
            return $user->isSuperAdmin();
        }

        return $user->hasPermissionTo('permissions.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return false;
        }

        // Check if permission is a system permission
        if (in_array($permission->name, [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'
        ])) {
            return false;
        }

        return $user->hasPermissionTo('permissions.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permissions.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return false;
        }

        // Check if permission is a system permission
        if (in_array($permission->name, [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'
        ])) {
            return false;
        }

        return $user->hasPermissionTo('permissions.delete');
    }

    /**
     * Determine whether the user can view permissions by module.
     */
    public function viewByModule(User $user): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can view permission statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can bulk create permissions.
     */
    public function bulkCreate(User $user): bool
    {
        return $user->hasPermissionTo('permissions.create');
    }

    /**
     * Determine whether the user can sync permissions with roles.
     */
    public function syncWithRoles(User $user): bool
    {
        return $user->hasPermissionTo('permissions.edit');
    }

    /**
     * Determine whether the user can manage permission assignments.
     */
    public function manageAssignments(User $user): bool
    {
        return $user->hasPermissionTo('permissions.edit');
    }

    /**
     * Determine whether the user can view permission roles.
     */
    public function viewRoles(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can manage permission hierarchy.
     */
    public function manageHierarchy(User $user): bool
    {
        // Only super admins can manage permission hierarchy
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view permission analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can export permission data.
     */
    public function exportData(User $user): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can manage permission templates.
     */
    public function manageTemplates(User $user): bool
    {
        return $user->hasPermissionTo('permissions.create');
    }

    /**
     * Determine whether the user can view permission history.
     */
    public function viewHistory(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can manage permission inheritance.
     */
    public function manageInheritance(User $user): bool
    {
        // Only super admins can manage permission inheritance
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can manage permission modules.
     */
    public function manageModules(User $user): bool
    {
        return $user->hasPermissionTo('permissions.edit');
    }

    /**
     * Determine whether the user can view permission usage.
     */
    public function viewUsage(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can manage permission dependencies.
     */
    public function manageDependencies(User $user): bool
    {
        // Only super admins can manage permission dependencies
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view permission reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }

    /**
     * Determine whether the user can manage permission categories.
     */
    public function manageCategories(User $user): bool
    {
        return $user->hasPermissionTo('permissions.edit');
    }

    /**
     * Determine whether the user can view permission relationships.
     */
    public function viewRelationships(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permissions.view');
    }
}
