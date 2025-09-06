<?php

namespace Modules\Admin\Services;

use Modules\Admin\Repositories\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Traits\HasPagination;

class AdminAuthService
{
    use HasPagination;
    
    protected $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Authenticate admin and generate token
     */
    public function authenticate(array $credentials, Request $request): array
    {
        // Check if remember me is requested
        $remember = isset($credentials['remember']) && $credentials['remember'];

        // Set longer expiration for remember me
        if ($remember) {
            config(['jwt.ttl' => config('jwt.remember_ttl', 43200)]);
        }

        // ✅ SINGLE AUTHENTICATION ATTEMPT - This validates both email and password
        $token = Auth::guard('admin')->claims([
            'type' => 'admin',
            'remember' => $remember
        ])->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ], $remember);

        // ✅ If authentication fails, throw exception
        if (!$token) {
            throw new CustomException('Invalid credentials or account is inactive', 401, [
                'email' => $credentials['email']
            ]);
        }

        // ✅ Get the authenticated admin
        $admin = Auth::guard('admin')->user();

        // Update last login only if authentication was successful
        $admin->updateLastLogin($request->ip(), $request->userAgent());

        // ✅ Refresh token with additional claims
        $token = Auth::guard('admin')->claims([
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'username' => $admin->username,
            'roles' => $admin->roles->pluck('name')->toArray(),
            'permissions' => $admin->getAllPermissions()->pluck('name')->toArray(),
            'type' => 'admin',
            'remember' => $remember
        ])->refresh();

        return [
            'admin' => $admin,
            'token' => $token,
            'permissions' => $this->getPermissions($admin),
            'roles' => $this->getRoles($admin)
        ];
    }

    /**
     * Get admin permissions
     */
    private function getPermissions($admin): array
    {
        try {
            return $admin->getAllPermissions()->pluck('name')->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get admin roles
     */
    private function getRoles($admin): array
    {
        try {
            return $admin->roles->pluck('name')->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Logout admin and invalidate token
     */
    public function logout(Request $request): array
    {
        try {
            $admin = Auth::guard('admin')->user();

            if ($admin) {
                // Invalidate the JWT token
                Auth::guard('admin')->logout();

                // Optionally update last logout timestamp if you add this field
                $admin->update(['last_logout_at' => now()]);
            }

            return [
                'message' => 'Logout successful',
                'admin_id' => $admin?->id
            ];
        } catch (\Exception $e) {
            // Even if there's an error, we should still try to logout
            Auth::guard('admin')->logout();

            return [
                'message' => 'Logout successful',
                'admin_id' => null
            ];
        }
    }

    /**
     * Get paginated admins list
     */
    public function getPaginatedAdmins(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        try {
            $paginationParams = $this->getPaginationParams($request, 'admin');

            return $this->adminRepository->paginate($paginationParams['per_page']);
        } catch (\Exception $e) {
            throw new CustomException('Failed to fetch admins list', 500);
        }
    }

    public function getTrashedAdmins(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        try {
            $paginationParams = $this->getPaginationParams($request, 'admin');
            $filters = $this->extractFilters($request);

            return $this->adminRepository->getTrashed($paginationParams['per_page'], $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching trashed admins', [
                'error' => $e->getMessage(),
                'filters' => $filters ?? []
            ]);

            throw new CustomException('Failed to fetch trashed admins', 500);
        }
    }

    /**
     * Extract filters from request
     */
    private function extractFilters(Request $request): array
    {
        $filters = [];

        if ($request->has('status')) {
            $filters['status'] = $request->get('status');
        }

        if ($request->has('role')) {
            $filters['role'] = $request->get('role');
        }

        return $filters;
    }
}