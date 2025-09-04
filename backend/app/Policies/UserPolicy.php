<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view') || $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.view') || 
               $user->hasRole(['admin', 'moderator']) || 
               $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.update') || 
               $user->hasRole('admin') || 
               $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.delete') || 
               ($user->hasRole('admin') && $user->id !== $model->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.restore') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.force-delete') || 
               ($user->hasRole('admin') && $user->id !== $model->id);
    }

    /**
     * Determine whether the user can upload avatar.
     */
    public function uploadAvatar(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.upload-avatar') || 
               $user->hasRole(['admin', 'moderator']) || 
               $user->id === $model->id;
    }

    /**
     * Determine whether the user can manage roles.
     */
    public function manageRoles(User $user): bool
    {
        return $user->hasPermissionTo('users.manage-roles') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage permissions.
     */
    public function managePermissions(User $user): bool
    {
        return $user->hasPermissionTo('users.manage-permissions') || $user->hasRole('admin');
    }
}
