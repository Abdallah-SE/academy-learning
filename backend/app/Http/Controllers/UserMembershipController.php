<?php

namespace App\Http\Controllers;

use App\Models\UserMembership;
use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\CustomException;

class UserMembershipController extends Controller
{
    /**
     * Display a listing of user memberships (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = UserMembership::with(['user', 'package']);

            // Apply filters
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('package_id')) {
                $query->where('package_id', $request->package_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->has('admin_verified')) {
                $query->where('admin_verified', $request->boolean('admin_verified'));
            }

            // Date range filters
            if ($request->has('start_date_from')) {
                $query->where('start_date', '>=', $request->start_date_from);
            }

            if ($request->has('start_date_to')) {
                $query->where('start_date', '<=', $request->start_date_to);
            }

            if ($request->has('end_date_from')) {
                $query->where('end_date', '>=', $request->end_date_from);
            }

            if ($request->has('end_date_to')) {
                $query->where('end_date', '<=', $request->end_date_to);
            }

            // Pagination
            $paginationParams = $this->getPaginationParams($request, 'membership');
            $memberships = $query->paginate($paginationParams['per_page']);

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => 'User memberships retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve user memberships: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created user membership
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'package_id' => 'required|exists:membership_packages,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'payment_method' => 'required|in:paypal,whatsapp',
                'payment_status' => 'sometimes|in:pending,completed,failed'
            ]);

            // Check if user already has an active membership
            $existingMembership = UserMembership::where('user_id', $validated['user_id'])
                ->where('status', 'active')
                ->first();

            if ($existingMembership) {
                throw new CustomException('User already has an active membership', 400);
            }

            $validated['payment_status'] = $validated['payment_status'] ?? 'pending';
            $validated['status'] = 'active';
            $validated['admin_verified'] = false;

            $membership = UserMembership::create($validated);

            return response()->json([
                'success' => true,
                'data' => $membership->load(['user', 'package']),
                'message' => 'User membership created successfully'
            ], 201);

        } catch (CustomException $e) {
            throw $e;
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to create user membership: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified user membership
     */
    public function show(UserMembership $membership): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $membership->load(['user', 'package']),
                'message' => 'User membership retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve user membership: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified user membership
     */
    public function update(Request $request, UserMembership $membership): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after:start_date',
                'status' => 'sometimes|in:active,expired,cancelled',
                'payment_status' => 'sometimes|in:pending,completed,failed',
                'admin_verified' => 'sometimes|boolean',
                'verified_at' => 'sometimes|date'
            ]);

            $membership->update($validated);

            return response()->json([
                'success' => true,
                'data' => $membership->fresh()->load(['user', 'package']),
                'message' => 'User membership updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to update user membership: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified user membership
     */
    public function destroy(UserMembership $membership): JsonResponse
    {
        try {
            $membership->delete();

            return response()->json([
                'success' => true,
                'message' => 'User membership deleted successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to delete user membership: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get current user's membership
     */
    public function myMembership(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $membership = $user->activeMembership;

            if (!$membership) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No active membership found'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $membership->load('package'),
                'message' => 'Active membership retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve membership: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get user's membership history
     */
    public function myMembershipHistory(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $memberships = $user->memberships()
                ->with('package')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => 'Membership history retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve membership history: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Subscribe to a membership package
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'package_id' => 'required|exists:membership_packages,id',
                'payment_method' => 'required|in:paypal,whatsapp'
            ]);

            $user = $request->user();
            $package = MembershipPackage::findOrFail($validated['package_id']);

            // Check if user already has an active membership
            if ($user->hasActiveMembership()) {
                throw new CustomException('You already have an active membership', 400);
            }

            // Calculate dates
            $startDate = now();
            $endDate = now()->addDays($package->duration_days);

            $membership = UserMembership::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'admin_verified' => false
            ]);

            return response()->json([
                'success' => true,
                'data' => $membership->load('package'),
                'message' => 'Subscription created successfully'
            ], 201);

        } catch (CustomException $e) {
            throw $e;
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to create subscription: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cancel user membership
     */
    public function cancel(Request $request, UserMembership $membership): JsonResponse
    {
        try {
            $user = $request->user();

            // Check if user owns this membership
            if ($membership->user_id !== $user->id) {
                throw new CustomException('Unauthorized to cancel this membership', 403);
            }

            if ($membership->status !== 'active') {
                throw new CustomException('Membership is not active', 400);
            }

            $membership->update([
                'status' => 'cancelled',
                'end_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Membership cancelled successfully'
            ]);

        } catch (CustomException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CustomException('Failed to cancel membership: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify payment (Admin only)
     */
    public function verifyPayment(Request $request, UserMembership $membership): JsonResponse
    {
        try {
            $validated = $request->validate([
                'verification_status' => 'required|in:approved,rejected',
                'notes' => 'nullable|string'
            ]);

            if ($validated['verification_status'] === 'approved') {
                $membership->update([
                    'payment_status' => 'completed',
                    'admin_verified' => true,
                    'verified_at' => now()
                ]);
            } else {
                $membership->update([
                    'payment_status' => 'failed',
                    'admin_verified' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $membership->fresh()->load(['user', 'package']),
                'message' => 'Payment verification updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to verify payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get membership statistics (Admin only)
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_memberships' => UserMembership::count(),
                'active_memberships' => UserMembership::where('status', 'active')->count(),
                'expired_memberships' => UserMembership::where('status', 'expired')->count(),
                'cancelled_memberships' => UserMembership::where('status', 'cancelled')->count(),
                'pending_payments' => UserMembership::where('payment_status', 'pending')->count(),
                'completed_payments' => UserMembership::where('payment_status', 'completed')->count(),
                'failed_payments' => UserMembership::where('payment_status', 'failed')->count(),
                'paypal_payments' => UserMembership::where('payment_method', 'paypal')->count(),
                'whatsapp_payments' => UserMembership::where('payment_method', 'whatsapp')->count(),
                'verified_payments' => UserMembership::where('admin_verified', true)->count(),
                'revenue_stats' => $this->getRevenueStats()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Membership statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve membership statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get revenue statistics
     */
    private function getRevenueStats(): array
    {
        $completedMemberships = UserMembership::where('payment_status', 'completed')
            ->with('package')
            ->get();

        $totalRevenue = $completedMemberships->sum('package.price');
        $monthlyRevenue = $completedMemberships
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('package.price');

        return [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'average_membership_value' => $completedMemberships->avg('package.price')
        ];
    }
}
