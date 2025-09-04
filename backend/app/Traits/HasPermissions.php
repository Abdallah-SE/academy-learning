<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

trait HasPermissions
{
    /**
     * Check if user has permission
     */
    protected function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasPermissionTo($permission);
    }

    /**
     * Check if user has role
     */
    protected function hasRole(string $role): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasRole($role);
    }

    /**
     * Check if user has any of the given roles
     */
    protected function hasAnyRole(array $roles): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole($roles);
    }

    /**
     * Check if user has all of the given roles
     */
    protected function hasAllRoles(array $roles): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasAllRoles($roles);
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is moderator
     */
    protected function isModerator(): bool
    {
        return $this->hasRole('moderator');
    }

    /**
     * Check if user is admin or moderator
     */
    protected function isAdminOrModerator(): bool
    {
        return $this->hasAnyRole(['admin', 'moderator']);
    }

    /**
     * Authorize user with permission
     */
    protected function authorizePermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            throw UnauthorizedException::forPermissions([$permission]);
        }
    }

    /**
     * Authorize user with role
     */
    protected function authorizeRole(string $role): void
    {
        if (!$this->hasRole($role)) {
            throw UnauthorizedException::forRoles([$role]);
        }
    }

    /**
     * Authorize user with any of the given roles
     */
    protected function authorizeAnyRole(array $roles): void
    {
        if (!$this->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }
    }

    /**
     * Get user permissions
     */
    protected function getUserPermissions(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        return $user->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Get user roles
     */
    protected function getUserRoles(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        return $user->getRoleNames()->toArray();
    }
}
