<?php

namespace Modules\Admin\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Http\Requests\V1\AdminLoginRequest;
use Modules\Admin\Http\Requests\V1\AdminUpdateProfileRequest;
use Modules\Admin\Repositories\Interfaces\AdminRepositoryInterface;
use Modules\Admin\Services\AdminAuthService;
use Modules\Admin\DTOs\AdminLoginDTO;
use Modules\Admin\Events\AdminLoggedIn;
use Modules\Admin\Http\Resources\V1\AdminCollection;

class AdminAuthController extends BaseController
{
    protected $adminRepository;
    protected $adminAuthService;

    public function __construct(
        AdminRepositoryInterface $adminRepository,
        AdminAuthService $adminAuthService
    ) {
        $this->adminRepository = $adminRepository;
        $this->adminAuthService = $adminAuthService;
    }

    /**
     * Admin login
     */
    public function login(AdminLoginRequest $request)
    {
        try {
            $dto = AdminLoginDTO::fromRequest($request->validated());

            $result = $this->adminAuthService->authenticate(
                $dto->toArray(),
                $request
            );
            // Get TTL based on remember me
            $remember = $dto->remember ?? false;
            $ttl = $remember ? config('jwt.remember_ttl', 43200) : config('jwt.ttl', 60);
            $isProduction = app()->environment('production');
            $isSecure = $isProduction || config('app.force_https', false);
            $sameSite = $isProduction ? 'None' : 'Lax';
            return $this->successResponse([
                'admin' => $this->formatAdminData($result['admin']),
                'token' => $result['token'],
                'token_type' => 'Bearer',
                'remember' =>  $dto->remember,
                'type' => 'admin',
                'expires_in' => $ttl * 60, // Convert minutes to seconds
                'refresh_token_expires_in' => config('jwt.refresh_ttl', 20160) * 60,
                'permissions' => $result['permissions'],
                'roles' => $result['roles']
            ], 'Login successful')->cookie(
                'token',
                $result['token'],
                $ttl * 60, // Convert minutes to seconds
                '/', // Path
                null, // Domain - null for current domain
                $isSecure, // ✅ Secure - true for production, false for localhost
                true,  // HttpOnly - always true for security
                false, // Raw
                $sameSite  // ✅ SameSite - 'None' for production, 'Lax' for localhost
            );
        } catch (\Exception $e) {
            $this->logError($e, 'Admin login error');
            return $this->handleException($e);
        }
    }

    /**
     * Admin logout
     */
    public function logout(Request $request)
    {
        try {
            $result = $this->adminAuthService->logout($request);

            return $this->successResponse([], $result['message'])->cookie('token', '', -1, '/');;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get admin profile
     */
    public function profile(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();

            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            return $this->successResponse([
                'admin' => $admin->load('roles', 'permissions')
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Update admin profile
     */
    public function updateProfile(AdminUpdateProfileRequest $request)
    {
        try {
            $admin = Auth::guard('admin')->user();

            if (!$admin) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $data = $request->validated();

            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
                $data['password_changed_at'] = now();
            }

            $admin = $this->adminRepository->update($admin->id, $data);

            $this->logInfo('Admin profile updated', [
                'admin_id' => $admin->id,
                'updated_fields' => array_keys($data)
            ]);

            return $this->successResponse([
                'admin' => $admin->load('roles', 'permissions')
            ], 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Refresh admin token
     */
    public function refresh(Request $request)
    {
        try {
            $token = Auth::guard('admin')->refresh();

            $admin = Auth::guard('admin')->user();

            return $this->successResponse([
                'admin' => $admin->only(['id', 'name', 'email', 'username', 'avatar', 'status']),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }




    /**
     * Format admin data for response
     */
    private function formatAdminData($admin): array
    {
        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'username' => $admin->username,
            'avatar' => $admin->avatar,
            'avatar_url' => $admin->avatar_url,
            'status' => $admin->status,
            'last_login_at' => $admin->last_login_at?->toISOString(),
            'email_verified_at' => $admin->email_verified_at?->toISOString(),
            'two_factor_enabled' => $admin->two_factor_enabled
        ];
    }

    /**
     * Get admin permissions
     */
    private function getAdminPermissions($admin): array
    {
        try {
            return $admin->getAllPermissions()->pluck('name')->toArray();
        } catch (\Exception $e) {
            $this->logInfo('Failed to get admin permissions', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get admin roles
     */
    private function getAdminRoles($admin): array
    {
        try {
            return $admin->roles->pluck('name')->toArray();
        } catch (\Exception $e) {
            $this->logInfo('Failed to get admin roles', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
