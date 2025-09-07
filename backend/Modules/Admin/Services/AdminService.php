<?php

namespace Modules\Admin\Services;

use Modules\Admin\Repositories\Interfaces\AdminRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;
use App\Services\Interfaces\ImageServiceInterface;
use App\Traits\HasPagination;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    use HasPagination;
    
    protected $adminRepository;
    protected $imageService;

    public function __construct(
        AdminRepositoryInterface $adminRepository,
        ImageServiceInterface $imageService
    ) {
        $this->adminRepository = $adminRepository;
        $this->imageService = $imageService;
    }

    /**
     * Get paginated admins list
     */
    public function getPaginatedAdmins(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        try {
            $paginationParams = $this->getPaginationParams($request, 'admin');
            $filters = $this->extractFilters($request);

            return $this->adminRepository->paginate($paginationParams['per_page'], $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated admins', [
                'error' => $e->getMessage(),
                'filters' => $filters ?? []
            ]);

            throw new CustomException('Failed to fetch admins list', 500);
        }
    }

    /**
     * Create new admin
     */
    public function createAdmin(array $data): array
    {
        try {
            // Extract roles before creating admin
            $roles = $data['roles'] ?? [];
            unset($data['roles']); // Remove roles from data array

            // Hash the password
            $data['password'] = Hash::make($data['password']);

            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'active';
            }

            // Set email verified at if not provided
            if (!isset($data['email_verified_at'])) {
                $data['email_verified_at'] = now();
            }

            // Set password changed at
            $data['password_changed_at'] = now();

            // Create the admin
            $admin = $this->adminRepository->create($data);

            // Assign roles if provided
            if (!empty($roles)) {
                $admin->assignRole($roles);
            } else {
                // Assign default 'moderator' role if no roles provided
                $admin->assignRole('moderator');
            }

            // Load relationships
            $admin->load('roles', 'permissions');

            Log::info('Admin created successfully', [
                'admin_id' => $admin->id,
                'email' => $admin->email,
                'roles' => $roles,
                'created_by' => auth('admin')->id() ?? 'system'
            ]);

            return [
                'admin' => $admin
            ];
        } catch (\Exception $e) {
            Log::error('Error creating admin', [
                'error' => $e->getMessage(),
                'data' => collect($data)->except(['password', 'password_confirmation'])->toArray()
            ]);

            throw new CustomException('Failed to create admin: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get admin by ID
     */
    public function getAdminById(int $id): array
    {
        try {
            $admin = $this->adminRepository->find($id);

            if (!$admin) {
                throw new CustomException('Admin not found', 404);
            }

            return [
                'admin' => $admin->load('roles', 'permissions')
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching admin by ID', [
                'admin_id' => $id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Update admin
     */
    public function updateAdmin(int $id, array $data): array
    {
        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
                $data['password_changed_at'] = now();
            }

            $admin = $this->adminRepository->update($id, $data); 

            return [
                'admin' => $admin->load('roles', 'permissions')
            ];
        } catch (\Exception $e) {
           

            throw new CustomException('Failed to update admin', 500);
        }
    }

    /**
     * Delete admin
     */
    public function deleteAdmin(int $id): array
    {
        try {
            $admin = $this->adminRepository->find($id);

            if (!$admin) {
                throw new CustomException('Admin not found', 404);
            }

            $this->adminRepository->delete($id);

            Log::info('Admin deleted successfully', [
                'admin_id' => $id,
                'email' => $admin->email
            ]);

            return [
                'message' => 'Admin deleted successfully'
            ];
        } catch (\Exception $e) {


            throw $e;
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

        if ($request->has('search')) {
            $filters['search'] = $request->get('search');
        }

        return $filters;
    }


    /**
     * Force delete admin permanently
     */
    public function forceDeleteAdmin(int $id): array
    {
        try {
            $admin = $this->adminRepository->findWithTrashed($id);

            if (!$admin) {
                throw new CustomException('Admin not found', 404);
            }

            // Delete avatar file if exists
            if ($admin->avatar) {
                $imageService = app(\App\Services\ImageService::class);
                $imageService->deleteImage($admin->avatar);
            }

            $this->adminRepository->forceDelete($id);

            Log::info('Admin force deleted permanently', [
                'admin_id' => $id,
                'email' => $admin->email
            ]);

            return [
                'message' => 'Admin permanently deleted'
            ];
        } catch (\Exception $e) {
            Log::error('Error force deleting admin', [
                'admin_id' => $id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Restore soft deleted admin
     */
    public function restoreAdmin(int $id): array
    {
        try {
            $admin = $this->adminRepository->findOnlyTrashed($id);

            if (!$admin) {
                throw new CustomException('Admin not found in trash', 404);
            }

            $this->adminRepository->restore($id);

            Log::info('Admin restored successfully', [
                'admin_id' => $id,
                'email' => $admin->email
            ]);

            return [
                'admin' => $admin->load('roles', 'permissions'),
                'message' => 'Admin restored successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Error restoring admin', [
                'admin_id' => $id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get soft deleted admins
     */
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
     * Get available roles
     */
    public function getAvailableRoles(): array
    {
        try {
            $roles = \Spatie\Permission\Models\Role::where('guard_name', 'admin')
                ->select('id', 'name', 'guard_name', 'created_at', 'updated_at')
                ->orderBy('name')
                ->get()
                ->toArray();

            return $roles;
        } catch (\Exception $e) {
            Log::error('Error fetching available roles', [
                'error' => $e->getMessage()
            ]);

            throw new CustomException('Failed to fetch available roles', 500);
        }
    }
}
