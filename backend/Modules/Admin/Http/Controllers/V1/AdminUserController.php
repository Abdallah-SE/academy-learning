<?php

namespace Modules\Admin\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\Interfaces\ImageServiceInterface;

class AdminUserController extends BaseController
{
    protected $imageService;

    public function __construct(ImageServiceInterface $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get all users with pagination and filtering
     */
    public function index(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $query = User::with(['roles', 'memberships']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by role
            if ($request->has('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->get('role'));
                });
            }

            // Sort functionality
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $paginationParams = $this->getPaginationParams($request, 'user');
            $users = $query->paginate($paginationParams['per_page']);

            $this->logInfo('Admin accessed users list', [
                'admin_id' => $admin->id,
                'filters' => $request->only(['search', 'status', 'role'])
            ]);

            return $this->paginatedResponse($users, 'Users retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get specific user details
     */
    public function show(Request $request, $userId)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $user = User::with(['roles', 'permissions', 'memberships.package'])
                       ->findOrFail($userId);

            $this->logInfo('Admin viewed user details', [
                'admin_id' => $admin->id,
                'user_id' => $userId
            ]);

            return $this->successResponse($user, 'User details retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'username' => 'nullable|string|unique:users,username',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|string|exists:roles,name',
                'status' => 'sometimes|in:active,inactive,suspended',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'] ?? null,
                'password' => bcrypt($validated['password']),
                'status' => $validated['status'] ?? 'active',
            ]);

            // Assign role
            $user->assignRole($validated['role']);

            $this->logInfo('Admin created new user', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            return $this->successResponse($user->load('roles'), 'User created successfully', 201);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $userId)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $user = User::findOrFail($userId);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $userId,
                'username' => 'sometimes|string|unique:users,username,' . $userId,
                'password' => 'sometimes|string|min:6|confirmed',
                'role' => 'sometimes|string|exists:roles,name',
                'status' => 'sometimes|in:active,inactive,suspended',
            ]);

            // Update user data
            $updateData = array_filter($validated, function ($key) {
                return in_array($key, ['name', 'email', 'username', 'status']);
            }, ARRAY_FILTER_USE_KEY);

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Update password if provided
            if (isset($validated['password'])) {
                $user->update(['password' => bcrypt($validated['password'])]);
            }

            // Update role if provided
            if (isset($validated['role'])) {
                $user->syncRoles([$validated['role']]);
            }

            $this->logInfo('Admin updated user', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'updated_fields' => array_keys($validated)
            ]);

            return $this->successResponse($user->load('roles'), 'User updated successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, $userId)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $user = User::findOrFail($userId);

            // Prevent admin from deleting themselves
            if ($user->id === $admin->id) {
                return $this->errorResponse('Cannot delete your own account', 400);
            }

            $user->delete();

            $this->logInfo('Admin deleted user', [
                'admin_id' => $admin->id,
                'user_id' => $userId,
                'user_email' => $user->email
            ]);

            return $this->successResponse([], 'User deleted successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get user statistics
     */
    public function statistics(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'suspended_users' => User::where('status', 'suspended')->count(),
                'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'users_this_year' => User::whereYear('created_at', now()->year)->count(),
                'role_distribution' => $this->getRoleDistribution(),
            ];

            $this->logInfo('Admin accessed user statistics', [
                'admin_id' => $admin->id
            ]);

            return $this->successResponse($stats, 'User statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get user activity log
     */
    public function activityLog(Request $request, $userId = null)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // This would typically fetch from a logging service
            $activityLog = [
                'recent_activities' => [],
                'login_history' => [],
                'action_history' => [],
            ];

            $this->logInfo('Admin accessed user activity log', [
                'admin_id' => $admin->id,
                'user_id' => $userId
            ]);

            return $this->successResponse($activityLog, 'User activity log retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $format = $request->get('format', 'csv');
            $filters = $request->only(['status', 'role']);

            // This would typically generate and return a file
            $exportData = [
                'format' => $format,
                'filters' => $filters,
                'download_url' => null,
                'total_records' => User::count(),
            ];

            $this->logInfo('Admin exported users data', [
                'admin_id' => $admin->id,
                'format' => $format,
                'filters' => $filters
            ]);

            return $this->successResponse($exportData, 'Users export initiated successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get role distribution
     */
    private function getRoleDistribution(): array
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $distribution = [];

        foreach ($roles as $role) {
            $distribution[$role->name] = User::whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role->name);
            })->count();
        }

        return $distribution;
    }
}
