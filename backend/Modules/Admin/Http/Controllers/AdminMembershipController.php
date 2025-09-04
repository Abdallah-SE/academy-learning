<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\MembershipPackage;
use App\Models\UserMembership;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AdminMembershipController extends BaseController
{
    /**
     * Get all membership packages with advanced filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', MembershipPackage::class);

            $paginationParams = $this->validatePaginationParams($this->getPaginationParams($request));
            $searchParams = $this->getSearchParams($request);

            $query = MembershipPackage::query()
                ->withCount(['memberships' => function ($query) {
                    $query->where('status', 'active');
                }]);

            // Search functionality
            if ($searchParams['search']) {
                $query->where(function ($q) use ($searchParams) {
                    $q->where('name', 'like', "%{$searchParams['search']}%")
                      ->orWhere('description', 'like', "%{$searchParams['search']}%");
                });
            }

            // Filter by status
            if (isset($searchParams['filters']['status'])) {
                $query->where('status', $searchParams['filters']['status']);
            }

            // Filter by price range
            if (isset($searchParams['filters']['price_min'])) {
                $query->where('price', '>=', $searchParams['filters']['price_min']);
            }

            if (isset($searchParams['filters']['price_max'])) {
                $query->where('price', '<=', $searchParams['filters']['price_max']);
            }

            // Filter by duration
            if (isset($searchParams['filters']['duration_days'])) {
                $query->where('duration_days', $searchParams['filters']['duration_days']);
            }

            $packages = $query->orderBy($paginationParams['sort_by'], $paginationParams['sort_direction'])
                            ->paginate($paginationParams['per_page']);

            $this->logInfo('Admin viewed membership packages', ['count' => $packages->count()]);

            return $this->paginatedResponse($packages, 'Membership packages retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving membership packages');
        }
    }

    /**
     * Get package details with statistics
     */
    public function show(int $id): JsonResponse
    {
        try {
            $package = MembershipPackage::with(['memberships' => function ($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            }])->find($id);

            if (!$package) {
                return $this->notFoundResponse('Membership package not found');
            }

            $this->authorize('view', $package);

            $packageData = $package->toArray();
            $packageData['statistics'] = [
                'total_memberships' => $package->memberships->count(),
                'active_memberships' => $package->memberships->where('status', 'active')->count(),
                'expired_memberships' => $package->memberships->where('expires_at', '<', now())->count(),
                'total_revenue' => $package->memberships->sum('amount'),
                'average_rating' => 0, // Implement rating system
            ];

            $this->logInfo('Admin viewed membership package details', ['package_id' => $package->id]);

            return $this->successResponse($packageData, 'Membership package details retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving membership package details');
        }
    }

    /**
     * Create new membership package
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->authorize('create', MembershipPackage::class);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
                'features' => 'required|array|min:1',
                'features.*' => 'string|max:255',
                'status' => 'string|in:active,inactive|default:active',
                'is_featured' => 'boolean|default:false',
                'max_users' => 'nullable|integer|min:1',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            $package = MembershipPackage::create($validated);

            $this->logInfo('Admin created membership package', [
                'admin_id' => $this->getCurrentUserId(),
                'package_id' => $package->id,
                'name' => $package->name
            ]);

            return $this->createdResponse($package, 'Membership package created successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Creating membership package');
        }
    }

    /**
     * Update membership package
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $package = MembershipPackage::find($id);

            if (!$package) {
                return $this->notFoundResponse('Membership package not found');
            }

            $this->authorize('update', $package);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'price' => 'sometimes|numeric|min:0',
                'duration_days' => 'sometimes|integer|min:1',
                'features' => 'sometimes|array|min:1',
                'features.*' => 'string|max:255',
                'status' => 'sometimes|string|in:active,inactive',
                'is_featured' => 'sometimes|boolean',
                'max_users' => 'nullable|integer|min:1',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            $package->update($validated);

            $this->logInfo('Admin updated membership package', [
                'admin_id' => $this->getCurrentUserId(),
                'package_id' => $package->id,
                'updated_fields' => array_keys($validated)
            ]);

            return $this->updatedResponse($package->fresh(), 'Membership package updated successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating membership package');
        }
    }

    /**
     * Delete membership package
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $package = MembershipPackage::find($id);

            if (!$package) {
                return $this->notFoundResponse('Membership package not found');
            }

            $this->authorize('delete', $package);

            // Check if package has active memberships
            if ($package->memberships()->where('status', 'active')->exists()) {
                return $this->errorResponse('Cannot delete package with active memberships', 400);
            }

            $package->delete();

            $this->logInfo('Admin deleted membership package', [
                'admin_id' => $this->getCurrentUserId(),
                'package_id' => $id
            ]);

            return $this->deletedResponse('Membership package deleted successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Deleting membership package');
        }
    }

    /**
     * Get all user memberships
     */
    public function userMemberships(Request $request): JsonResponse
    {
        try {
            $this->authorize('access-admin-panel');

            $paginationParams = $this->validatePaginationParams($this->getPaginationParams($request));
            $searchParams = $this->getSearchParams($request);

            $query = UserMembership::with(['user', 'package']);

            // Search by user name or email
            if ($searchParams['search']) {
                $query->whereHas('user', function ($q) use ($searchParams) {
                    $q->where('name', 'like', "%{$searchParams['search']}%")
                      ->orWhere('email', 'like', "%{$searchParams['search']}%");
                });
            }

            // Filter by status
            if (isset($searchParams['filters']['status'])) {
                $query->where('status', $searchParams['filters']['status']);
            }

            // Filter by package
            if (isset($searchParams['filters']['package_id'])) {
                $query->where('membership_package_id', $searchParams['filters']['package_id']);
            }

            // Filter by date range
            if ($searchParams['date_from']) {
                $query->whereDate('created_at', '>=', $searchParams['date_from']);
            }

            if ($searchParams['date_to']) {
                $query->whereDate('created_at', '<=', $searchParams['date_to']);
            }

            // Filter by expiry
            if (isset($searchParams['filters']['expired'])) {
                if ($searchParams['filters']['expired']) {
                    $query->where('expires_at', '<', now());
                } else {
                    $query->where('expires_at', '>=', now());
                }
            }

            $memberships = $query->orderBy($paginationParams['sort_by'], $paginationParams['sort_direction'])
                               ->paginate($paginationParams['per_page']);

            return $this->paginatedResponse($memberships, 'User memberships retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving user memberships');
        }
    }

    /**
     * Assign membership to user
     */
    public function assignMembership(Request $request): JsonResponse
    {
        try {
            $this->authorize('access-admin-panel');

            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'package_id' => 'required|integer|exists:membership_packages,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:500',
            ]);

            $user = User::find($validated['user_id']);
            $package = MembershipPackage::find($validated['package_id']);

            if (!$user || !$package) {
                return $this->notFoundResponse('User or package not found');
            }

            // Check if user already has active membership
            $existingMembership = $user->memberships()->where('status', 'active')->first();
            if ($existingMembership) {
                return $this->errorResponse('User already has an active membership', 400);
            }

            $startDate = $validated['start_date'] ?? now();
            $endDate = $validated['end_date'] ?? now()->addDays($package->duration_days);
            $amount = $validated['amount'] ?? $package->price;

            $membership = UserMembership::create([
                'user_id' => $user->id,
                'membership_package_id' => $package->id,
                'start_date' => $startDate,
                'expires_at' => $endDate,
                'amount' => $amount,
                'status' => 'active',
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->logInfo('Admin assigned membership to user', [
                'admin_id' => $this->getCurrentUserId(),
                'user_id' => $user->id,
                'package_id' => $package->id,
                'membership_id' => $membership->id
            ]);

            return $this->createdResponse($membership->load('user', 'package'), 'Membership assigned successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Assigning membership to user');
        }
    }

    /**
     * Update user membership
     */
    public function updateUserMembership(Request $request, int $id): JsonResponse
    {
        try {
            $membership = UserMembership::with('user', 'package')->find($id);

            if (!$membership) {
                return $this->notFoundResponse('User membership not found');
            }

            $this->authorize('access-admin-panel');

            $validated = $request->validate([
                'status' => 'sometimes|string|in:active,inactive,expired,cancelled',
                'expires_at' => 'sometimes|date|after:now',
                'amount' => 'sometimes|numeric|min:0',
                'notes' => 'nullable|string|max:500',
            ]);

            $membership->update($validated);

            $this->logInfo('Admin updated user membership', [
                'admin_id' => $this->getCurrentUserId(),
                'membership_id' => $membership->id,
                'updated_fields' => array_keys($validated)
            ]);

            return $this->updatedResponse($membership->fresh(), 'User membership updated successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating user membership');
        }
    }

    /**
     * Cancel user membership
     */
    public function cancelMembership(int $id): JsonResponse
    {
        try {
            $membership = UserMembership::find($id);

            if (!$membership) {
                return $this->notFoundResponse('User membership not found');
            }

            $this->authorize('access-admin-panel');

            $membership->update([
                'status' => 'cancelled',
                'expires_at' => now(),
            ]);

            $this->logInfo('Admin cancelled user membership', [
                'admin_id' => $this->getCurrentUserId(),
                'membership_id' => $membership->id,
                'user_id' => $membership->user_id
            ]);

            return $this->successResponse($membership->fresh(), 'Membership cancelled successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Cancelling membership');
        }
    }

    /**
     * Get membership statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $this->authorize('access-admin-panel');

            $stats = [
                'packages' => [
                    'total_packages' => MembershipPackage::count(),
                    'active_packages' => MembershipPackage::where('status', 'active')->count(),
                    'featured_packages' => MembershipPackage::where('is_featured', true)->count(),
                ],
                'memberships' => [
                    'total_memberships' => UserMembership::count(),
                    'active_memberships' => UserMembership::where('status', 'active')->count(),
                    'expired_memberships' => UserMembership::where('expires_at', '<', now())->count(),
                    'cancelled_memberships' => UserMembership::where('status', 'cancelled')->count(),
                ],
                'revenue' => [
                    'total_revenue' => UserMembership::sum('amount'),
                    'monthly_revenue' => UserMembership::whereMonth('created_at', now()->month)->sum('amount'),
                    'average_package_price' => MembershipPackage::avg('price') ?? 0,
                ],
                'trends' => [
                    'new_memberships_this_month' => UserMembership::whereMonth('created_at', now()->month)->count(),
                    'expiring_this_week' => UserMembership::whereBetween('expires_at', [now(), now()->addWeek()])->count(),
                    'popular_packages' => $this->getPopularPackages(),
                ],
            ];

            return $this->successResponse($stats, 'Membership statistics retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving membership statistics');
        }
    }

    /**
     * Bulk package operations
     */
    public function bulkPackageOperations(Request $request): JsonResponse
    {
        try {
            $this->authorize('access-admin-panel');

            $validated = $request->validate([
                'action' => 'required|string|in:activate,deactivate,delete,feature,unfeature',
                'package_ids' => 'required|array|min:1',
                'package_ids.*' => 'integer|exists:membership_packages,id',
            ]);

            $action = $validated['action'];
            $packageIds = $validated['package_ids'];
            $results = [];

            foreach ($packageIds as $packageId) {
                $package = MembershipPackage::find($packageId);
                
                if (!$package) {
                    $results[$packageId] = ['success' => false, 'message' => 'Package not found'];
                    continue;
                }

                try {
                    switch ($action) {
                        case 'activate':
                            $package->update(['status' => 'active']);
                            $results[$packageId] = ['success' => true, 'message' => 'Package activated'];
                            break;

                        case 'deactivate':
                            $package->update(['status' => 'inactive']);
                            $results[$packageId] = ['success' => true, 'message' => 'Package deactivated'];
                            break;

                        case 'delete':
                            if ($package->memberships()->where('status', 'active')->exists()) {
                                $results[$packageId] = ['success' => false, 'message' => 'Package has active memberships'];
                            } else {
                                $package->delete();
                                $results[$packageId] = ['success' => true, 'message' => 'Package deleted'];
                            }
                            break;

                        case 'feature':
                            $package->update(['is_featured' => true]);
                            $results[$packageId] = ['success' => true, 'message' => 'Package featured'];
                            break;

                        case 'unfeature':
                            $package->update(['is_featured' => false]);
                            $results[$packageId] = ['success' => true, 'message' => 'Package unfeatured'];
                            break;
                    }
                } catch (Exception $e) {
                    $results[$packageId] = ['success' => false, 'message' => $e->getMessage()];
                }
            }

            $this->logInfo('Admin performed bulk package operations', [
                'admin_id' => $this->getCurrentUserId(),
                'action' => $action,
                'package_ids' => $packageIds,
                'results' => $results
            ]);

            return $this->successResponse($results, 'Bulk package operations completed');
        } catch (Exception $e) {
            return $this->handleException($e, 'Performing bulk package operations');
        }
    }

    /**
     * Get popular packages
     */
    private function getPopularPackages(): array
    {
        return MembershipPackage::withCount(['memberships' => function ($query) {
            $query->where('status', 'active');
        }])
        ->orderBy('memberships_count', 'desc')
        ->take(5)
        ->get(['id', 'name', 'price', 'memberships_count'])
        ->toArray();
    }
}
