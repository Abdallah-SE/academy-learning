<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Exceptions\CustomException;

class RoleController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of roles
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->authorize('roles.view');

            $query = Role::with('permissions');

            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('display_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('level')) {
                $query->where('level', $request->level);
            }

            // Pagination
            $paginationParams = $this->getPaginationParams($request, 'default');
            $roles = $query->paginate($paginationParams['per_page']);

            return response()->json([
                'success' => true,
                'data' => $roles,
                'message' => 'Roles retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve roles: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->authorize('roles.create');

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'level' => 'required|integer|min:1|max:10',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
                'level' => $validated['level'],
                'guard_name' => 'web'
            ]);

            if (!empty($validated['permissions'])) {
                $role->givePermissionTo($validated['permissions']);
            }

            return response()->json([
                'success' => true,
                'data' => $role->load('permissions'),
                'message' => 'Role created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to create role: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified role
     */
    public function show(Role $role): JsonResponse
    {
        try {
            $this->authorize('roles.view');

            return response()->json([
                'success' => true,
                'data' => $role->load('permissions'),
                'message' => 'Role retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve role: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        try {
            $this->authorize('roles.edit');

            $validated = $request->validate([
                'display_name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'level' => 'sometimes|integer|min:1|max:10',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            $role->update($validated);

            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            return response()->json([
                'success' => true,
                'data' => $role->fresh()->load('permissions'),
                'message' => 'Role updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to update role: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            $this->authorize('roles.delete');

            // Check if role is assigned to any users
            if ($role->users()->count() > 0) {
                throw new CustomException('Cannot delete role that is assigned to users', 400);
            }

            // Check if role is a system role
            if (in_array($role->name, ['student', 'teacher', 'admin', 'super_admin'])) {
                throw new CustomException('Cannot delete system roles', 400);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);

        } catch (CustomException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CustomException('Failed to delete role: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get all permissions for role assignment
     */
    public function permissions(): JsonResponse
    {
        try {
            $this->authorize('roles.view');

            $permissions = Permission::orderBy('name')->get();

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
     * Assign permissions to role
     */
    public function assignPermissions(Request $request, Role $role): JsonResponse
    {
        try {
            $this->authorize('roles.edit');

            $validated = $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            $role->syncPermissions($validated['permissions']);

            return response()->json([
                'success' => true,
                'data' => $role->fresh()->load('permissions'),
                'message' => 'Permissions assigned successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to assign permissions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get role statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $this->authorize('roles.view');

            $stats = [
                'total_roles' => Role::count(),
                'roles_by_level' => Role::selectRaw('level, count(*) as count')
                    ->groupBy('level')
                    ->orderBy('level')
                    ->get(),
                'roles_with_users' => Role::withCount('users')->get(),
                'permission_distribution' => $this->getPermissionDistribution()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Role statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve role statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get permission distribution across roles
     */
    private function getPermissionDistribution(): array
    {
        $roles = Role::with('permissions')->get();
        $distribution = [];

        foreach ($roles as $role) {
            $distribution[$role->name] = [
                'permission_count' => $role->permissions->count(),
                'permissions' => $role->permissions->pluck('name')
            ];
        }

        return $distribution;
    }

    /**
     * Duplicate a role
     */
    public function duplicate(Role $role): JsonResponse
    {
        try {
            $this->authorize('roles.create');

            $newRole = $role->replicate();
            $newRole->name = $role->name . '_copy_' . time();
            $newRole->display_name = $role->display_name . ' (Copy)';
            $newRole->save();

            // Copy permissions
            $newRole->syncPermissions($role->permissions);

            return response()->json([
                'success' => true,
                'data' => $newRole->load('permissions'),
                'message' => 'Role duplicated successfully'
            ], 201);

        } catch (\Exception $e) {
            throw new CustomException('Failed to duplicate role: ' . $e->getMessage(), 500);
        }
    }
}
