<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Exceptions\CustomException;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of permissions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->authorize('permissions.view');

            $query = Permission::query();

            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('module')) {
                $query->where('name', 'like', $request->module . '.%');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $permissions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $permissions,
                'message' => 'Permissions retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve permissions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->authorize('permissions.create');

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name',
                'description' => 'required|string',
                'guard_name' => 'sometimes|string|max:255'
            ]);

            $validated['guard_name'] = $validated['guard_name'] ?? 'web';

            $permission = Permission::create($validated);

            return response()->json([
                'success' => true,
                'data' => $permission,
                'message' => 'Permission created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to create permission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission): JsonResponse
    {
        try {
            $this->authorize('permissions.view');

            return response()->json([
                'success' => true,
                'data' => $permission->load('roles'),
                'message' => 'Permission retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve permission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        try {
            $this->authorize('permissions.edit');

            $validated = $request->validate([
                'description' => 'sometimes|string',
                'guard_name' => 'sometimes|string|max:255'
            ]);

            $permission->update($validated);

            return response()->json([
                'success' => true,
                'data' => $permission->fresh(),
                'message' => 'Permission updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to update permission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission): JsonResponse
    {
        try {
            $this->authorize('permissions.delete');

            // Check if permission is assigned to any roles
            if ($permission->roles()->count() > 0) {
                throw new CustomException('Cannot delete permission that is assigned to roles', 400);
            }

            // Check if permission is a system permission
            if (in_array($permission->name, [
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete'
            ])) {
                throw new CustomException('Cannot delete system permissions', 400);
            }

            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);

        } catch (CustomException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CustomException('Failed to delete permission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get permissions by module
     */
    public function byModule(): JsonResponse
    {
        try {
            $this->authorize('permissions.view');

            $permissions = Permission::orderBy('name')->get();
            $modules = [];

            foreach ($permissions as $permission) {
                $parts = explode('.', $permission->name);
                $module = $parts[0] ?? 'other';
                
                if (!isset($modules[$module])) {
                    $modules[$module] = [];
                }
                
                $modules[$module][] = $permission;
            }

            return response()->json([
                'success' => true,
                'data' => $modules,
                'message' => 'Permissions grouped by module retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve permissions by module: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get permission statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $this->authorize('permissions.view');

            $stats = [
                'total_permissions' => Permission::count(),
                'permissions_by_module' => $this->getPermissionsByModule(),
                'permissions_with_roles' => Permission::withCount('roles')->get(),
                'unused_permissions' => Permission::whereDoesntHave('roles')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Permission statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve permission statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get permissions grouped by module
     */
    private function getPermissionsByModule(): array
    {
        $permissions = Permission::orderBy('name')->get();
        $modules = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? 'other';
            
            if (!isset($modules[$module])) {
                $modules[$module] = 0;
            }
            
            $modules[$module]++;
        }

        return $modules;
    }

    /**
     * Bulk create permissions
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        try {
            $this->authorize('permissions.create');

            $validated = $request->validate([
                'permissions' => 'required|array|min:1',
                'permissions.*.name' => 'required|string|max:255|unique:permissions,name',
                'permissions.*.description' => 'required|string',
                'permissions.*.guard_name' => 'nullable|string|max:255'
            ]);

            $createdPermissions = [];

            foreach ($validated['permissions'] as $permissionData) {
                $permissionData['guard_name'] = $permissionData['guard_name'] ?? 'web';
                $createdPermissions[] = Permission::create($permissionData);
            }

            return response()->json([
                'success' => true,
                'data' => $createdPermissions,
                'message' => count($createdPermissions) . ' permissions created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to bulk create permissions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Sync permissions with roles
     */
    public function syncWithRoles(Request $request): JsonResponse
    {
        try {
            $this->authorize('permissions.edit');

            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            $role = \Spatie\Permission\Models\Role::findOrFail($validated['role_id']);
            $role->syncPermissions($validated['permissions']);

            return response()->json([
                'success' => true,
                'data' => $role->fresh()->load('permissions'),
                'message' => 'Permissions synced with role successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to sync permissions: ' . $e->getMessage(), 500);
        }
    }
}
