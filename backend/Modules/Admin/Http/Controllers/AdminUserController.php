<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class AdminUserController extends BaseController
{
    /**
     * Get all users with advanced filtering and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', User::class);

            $paginationParams = $this->validatePaginationParams($this->getPaginationParams($request));
            $searchParams = $this->getSearchParams($request);

            $query = User::query()
                ->with(['memberships' => function ($query) {
                    $query->with('package');
                }]);

            // Search functionality
            if ($searchParams['search']) {
                $query->where(function ($q) use ($searchParams) {
                    $q->where('name', 'like', "%{$searchParams['search']}%")
                      ->orWhere('email', 'like', "%{$searchParams['search']}%")
                      ->orWhere('id', 'like', "%{$searchParams['search']}%");
                });
            }

            // Filter by role
            if (isset($searchParams['filters']['role'])) {
                $query->where('role', $searchParams['filters']['role']);
            }

            // Filter by status
            if (isset($searchParams['filters']['status'])) {
                $query->where('status', $searchParams['filters']['status']);
            }

            // Filter by membership status
            if (isset($searchParams['filters']['membership_status'])) {
                $query->whereHas('memberships', function ($q) use ($searchParams) {
                    $q->where('status', $searchParams['filters']['membership_status']);
                });
            }

            // Date range filters
            if ($searchParams['date_from']) {
                $query->whereDate('created_at', '>=', $searchParams['date_from']);
            }

            if ($searchParams['date_to']) {
                $query->whereDate('created_at', '<=', $searchParams['date_to']);
            }

            // Last login filter
            if (isset($searchParams['filters']['last_login'])) {
                $query->whereNotNull('last_login_at');
            }

            $users = $query->orderBy($paginationParams['sort_by'], $paginationParams['sort_direction'])
                          ->paginate($paginationParams['per_page']);

            $this->logInfo('Admin viewed users list', ['count' => $users->count()]);

            return $this->paginatedResponse($users, 'Users retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving users list');
        }
    }

    /**
     * Get user details with full information
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::with(['memberships' => function ($query) {
                $query->with('package')->orderBy('created_at', 'desc');
            }])->find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('view', $user);

            $userData = $user->toArray();
            $userData['membership_history'] = $user->memberships;
            $userData['statistics'] = [
                'total_memberships' => $user->memberships->count(),
                'active_memberships' => $user->memberships->where('status', 'active')->count(),
                'expired_memberships' => $user->memberships->where('expires_at', '<', now())->count(),
            ];

            $this->logInfo('Admin viewed user details', ['user_id' => $user->id]);

            return $this->successResponse($userData, 'User details retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving user details');
        }
    }

    /**
     * Create new user by admin
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->authorize('create', User::class);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
                'role' => 'required|string|in:admin,moderator,user',
                'status' => 'string|in:active,inactive,suspended|default:active',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'bio' => 'nullable|string|max:1000',
            ]);

            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            $this->logInfo('Admin created new user', [
                'admin_id' => $this->getCurrentUserId(),
                'user_id' => $user->id,
                'role' => $user->role
            ]);

            return $this->createdResponse($user, 'User created successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Creating user');
        }
    }

    /**
     * Update user by admin
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('update', $user);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8|confirmed',
                'password_confirmation' => 'required_with:password|string|min:8',
                'role' => 'sometimes|string|in:admin,moderator,user',
                'status' => 'sometimes|string|in:active,inactive,suspended',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'bio' => 'nullable|string|max:1000',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            $this->logInfo('Admin updated user', [
                'admin_id' => $this->getCurrentUserId(),
                'user_id' => $user->id,
                'updated_fields' => array_keys($validated)
            ]);

            return $this->updatedResponse($user->fresh(), 'User updated successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating user');
        }
    }

    /**
     * Delete user by admin
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('delete', $user);

            // Check if user has active memberships
            if ($user->memberships()->where('status', 'active')->exists()) {
                return $this->errorResponse('Cannot delete user with active memberships', 400);
            }

            $user->delete();

            $this->logInfo('Admin deleted user', [
                'admin_id' => $this->getCurrentUserId(),
                'user_id' => $id
            ]);

            return $this->deletedResponse('User deleted successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Deleting user');
        }
    }

    /**
     * Bulk user operations
     */
    public function bulkOperations(Request $request): JsonResponse
    {
        try {
            $this->authorize('access-admin-panel');

            $validated = $request->validate([
                'action' => 'required|string|in:activate,deactivate,suspend,delete,change_role,export',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer|exists:users,id',
                'role' => 'required_if:action,change_role|string|in:admin,moderator,user',
                'reason' => 'nullable|string|max:500',
            ]);

            $action = $validated['action'];
            $userIds = $validated['user_ids'];
            $results = [];

            foreach ($userIds as $userId) {
                $user = User::find($userId);
                
                if (!$user) {
                    $results[$userId] = ['success' => false, 'message' => 'User not found'];
                    continue;
                }

                try {
                    switch ($action) {
                        case 'activate':
                            $user->update(['status' => 'active']);
                            $results[$userId] = ['success' => true, 'message' => 'User activated'];
                            break;

                        case 'deactivate':
                            $user->update(['status' => 'inactive']);
                            $results[$userId] = ['success' => true, 'message' => 'User deactivated'];
                            break;

                        case 'suspend':
                            $user->update(['status' => 'suspended']);
                            $results[$userId] = ['success' => true, 'message' => 'User suspended'];
                            break;

                        case 'delete':
                            if ($user->memberships()->where('status', 'active')->exists()) {
                                $results[$userId] = ['success' => false, 'message' => 'User has active memberships'];
                            } else {
                                $user->delete();
                                $results[$userId] = ['success' => true, 'message' => 'User deleted'];
                            }
                            break;

                        case 'change_role':
                            $user->update(['role' => $validated['role']]);
                            $results[$userId] = ['success' => true, 'message' => 'Role changed to ' . $validated['role']];
                            break;

                        case 'export':
                            $results[$userId] = ['success' => true, 'data' => $user->toArray()];
                            break;
                    }
                } catch (Exception $e) {
                    $results[$userId] = ['success' => false, 'message' => $e->getMessage()];
                }
            }

            $this->logInfo('Admin performed bulk user operations', [
                'admin_id' => $this->getCurrentUserId(),
                'action' => $action,
                'user_ids' => $userIds,
                'results' => $results
            ]);

            return $this->successResponse($results, 'Bulk operations completed');
        } catch (Exception $e) {
            return $this->handleException($e, 'Performing bulk user operations');
        }
    }

    /**
     * Get user statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $this->authorize('viewAny', User::class);

            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'suspended_users' => User::where('status', 'suspended')->count(),
                'users_by_role' => [
                    'admin' => User::where('role', 'admin')->count(),
                    'moderator' => User::where('role', 'moderator')->count(),
                    'user' => User::where('role', 'user')->count(),
                ],
                'users_with_memberships' => User::whereHas('memberships')->count(),
                'users_without_memberships' => User::whereDoesntHave('memberships')->count(),
                'recent_registrations' => User::latest()->take(10)->get(['id', 'name', 'email', 'created_at']),
                'registration_trend' => $this->getRegistrationTrend(),
            ];

            return $this->successResponse($stats, 'User statistics retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving user statistics');
        }
    }

    /**
     * Get user activity log
     */
    public function activityLog(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('view', $user);

            $activityLog = [
                'user_info' => $user->only(['id', 'name', 'email', 'role', 'status']),
                'membership_history' => $user->memberships()->with('package')->orderBy('created_at', 'desc')->get(),
                'login_history' => [], // Implement login tracking
                'activity_summary' => [
                    'total_memberships' => $user->memberships->count(),
                    'active_memberships' => $user->memberships->where('status', 'active')->count(),
                    'total_spent' => $user->memberships->sum('amount'),
                    'last_activity' => $user->updated_at,
                ],
            ];

            return $this->successResponse($activityLog, 'User activity log retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving user activity log');
        }
    }

    /**
     * Export users data
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $this->authorize('export-data');

            $validated = $request->validate([
                'format' => 'string|in:json,csv,xml|default:json',
                'filters' => 'array',
                'fields' => 'array',
            ]);

            $format = $validated['format'] ?? 'json';
            $filters = $validated['filters'] ?? [];
            $fields = $validated['fields'] ?? ['id', 'name', 'email', 'role', 'status', 'created_at'];

            $query = User::query();

            // Apply filters
            if (isset($filters['role'])) {
                $query->where('role', $filters['role']);
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $users = $query->select($fields)->get();

            $this->logInfo('Admin exported users data', [
                'admin_id' => $this->getCurrentUserId(),
                'format' => $format,
                'count' => $users->count()
            ]);

            return $this->successResponse($users, 'Users data exported successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Exporting users data');
        }
    }

    /**
     * Get registration trend data
     */
    private function getRegistrationTrend(): array
    {
        $trend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('created_at', $date)->count();
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count
            ];
        }

        return $trend;
    }
}
