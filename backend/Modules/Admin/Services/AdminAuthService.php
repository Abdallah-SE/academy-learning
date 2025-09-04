<?php

namespace Modules\Admin\Services;

use Modules\Admin\Repositories\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;

class AdminAuthService
{
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

        // Check if admin exists and is active
        $admin = $this->adminRepository->findByEmail($credentials['email']);


        if (!$admin) {
            throw new CustomException('Invalid credentials or account is inactive', 401, [
                'email' => $credentials['email'],
                'admin_status' => $admin?->status
            ]);
        }


        // Check if remember me is requested
        $remember = isset($credentials['remember']) && $credentials['remember'];
      

        $admin->updateLastLogin($request->ip(), $request->userAgent());

        // Generate token
        $token = $this->generateToken($admin, $remember);


        return [
            'admin' => $admin,
            'token' => $token,
            'permissions' => $this->getPermissions($admin),
            'roles' => $this->getRoles($admin)
        ];
    }

    /**
     * Generate JWT token with custom claims
     */
    private function generateToken($admin, bool $remember = false): string
    {
        $customClaims = [
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'username' => $admin->username,
            'roles' => $admin->roles->pluck('name')->toArray(),
            'permissions' => $admin->getAllPermissions()->pluck('name')->toArray(),
            'type' => 'admin',
            'remember' => $remember

        ];
        // Set longer expiration for remember me
        if ($remember) {
            // Use longer TTL for remember me (30 days)
            config(['jwt.ttl' => config('jwt.remember_ttl', 43200)]);
        }
        return Auth::guard('admin')->claims($customClaims)->attempt([
            'email' => $admin->email,
            'password' => request('password')
        ]);
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
            $perPage = $request->get('per_page', 15);

            return $this->adminRepository->paginate($perPage);
        } catch (\Exception $e) {

            throw new CustomException('Failed to fetch admins list', 500);
        }
    }
}
