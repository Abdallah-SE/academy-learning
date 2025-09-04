<?php

namespace Modules\Admin\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\UserMembership;

class AdminDashboardController extends BaseController
{
    protected $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Get dashboard overview
     */
    public function overview(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $data = $this->adminRepository->getDashboardData();

            $this->logInfo('Admin dashboard overview accessed', [
                'admin_id' => $admin->id
            ]);

            return $this->successResponse($data, 'Dashboard overview retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get dashboard statistics
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
                'total_packages' => MembershipPackage::count(),
                'active_packages' => MembershipPackage::where('status', 'active')->count(),
                'total_memberships' => UserMembership::count(),
                'active_memberships' => UserMembership::where('status', 'active')->count(),
                'revenue_this_month' => UserMembership::whereMonth('created_at', now()->month)->sum('amount'),
                'revenue_this_year' => UserMembership::whereYear('created_at', now()->year)->sum('amount'),
            ];

            $this->logInfo('Admin dashboard statistics accessed', [
                'admin_id' => $admin->id
            ]);

            return $this->successResponse($stats, 'Dashboard statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get analytics data
     */
    public function analytics(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $analytics = [
                'user_growth' => $this->getUserGrowthData(),
                'membership_growth' => $this->getMembershipGrowthData(),
                'revenue_trends' => $this->getRevenueTrendsData(),
                'top_packages' => $this->getTopPackagesData(),
            ];

            $this->logInfo('Admin dashboard analytics accessed', [
                'admin_id' => $admin->id
            ]);

            return $this->successResponse($analytics, 'Analytics data retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get system logs
     */
    public function systemLogs(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // This would typically fetch from a logging service
            $logs = [
                'recent_activities' => [],
                'error_logs' => [],
                'access_logs' => [],
            ];

            $this->logInfo('Admin system logs accessed', [
                'admin_id' => $admin->id
            ]);

            return $this->successResponse($logs, 'System logs retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get notifications
     */
    public function notifications(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $notifications = [
                'unread_count' => 0,
                'notifications' => [],
            ];

            $this->logInfo('Admin notifications accessed', [
                'admin_id' => $admin->id
            ]);

            return $this->successResponse($notifications, 'Notifications retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Export data
     */
    public function exportData(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $type = $request->get('type', 'users');
            $format = $request->get('format', 'csv');

            // This would typically generate and return a file
            $exportData = [
                'type' => $type,
                'format' => $format,
                'download_url' => null,
            ];

            $this->logInfo('Admin data export requested', [
                'admin_id' => $admin->id,
                'type' => $type,
                'format' => $format
            ]);

            return $this->successResponse($exportData, 'Data export initiated successfully');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get user growth data
     */
    private function getUserGrowthData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [10, 15, 25, 30, 45, 60],
        ];
    }

    /**
     * Get membership growth data
     */
    private function getMembershipGrowthData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [5, 8, 12, 18, 25, 35],
        ];
    }

    /**
     * Get revenue trends data
     */
    private function getRevenueTrendsData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [1000, 1500, 2500, 3000, 4500, 6000],
        ];
    }

    /**
     * Get top packages data
     */
    private function getTopPackagesData(): array
    {
        return [
            'labels' => ['Basic', 'Premium', 'Pro', 'Enterprise'],
            'data' => [30, 25, 20, 15],
        ];
    }
}
