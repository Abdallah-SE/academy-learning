<?php

namespace Modules\Admin\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\Admin\Services\AdminService;
use Modules\Admin\Http\Requests\V1\CreateAdminRequest;
use Modules\Admin\Http\Requests\V1\UpdateAdminRequest;
use Modules\Admin\Http\Resources\V1\AdminResource;
use Modules\Admin\Http\Resources\V1\AdminCollection;
use Modules\Admin\Models\Admin;

class AdminController extends BaseController
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Get paginated admins list
     */
    public function index(Request $request)
    {
        try {
            $paginatedAdmins = $this->adminService->getPaginatedAdmins($request);

            return (new AdminCollection($paginatedAdmins))
                ->additional([
                    'message' => 'Admins list retrieved successfully'
                ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Create new admin
     */
    public function store(CreateAdminRequest $request)
    {
        try {
            $result = $this->adminService->createAdmin($request->validated());

            return (new AdminResource($result['admin']))
                ->additional([
                    'message' => 'Admin created successfully'
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            $this->logError($e, 'Error creating admin');
            return $this->handleException($e);
        }
    }

    /**
     * Get admin by ID
     */
    public function show(int $id)
    {
        try {
            $result = $this->adminService->getAdminById($id);

            return (new AdminResource($result['admin']))
                ->additional([
                    'message' => 'Admin retrieved successfully'
                ]);
        } catch (\Exception $e) {
            $this->logError($e, 'Error fetching admin');
            return $this->handleException($e);
        }
    }

    /**
     * Update admin
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {

        try {
            $result = $this->adminService->updateAdmin($admin->id, $request->validated());

            return (new AdminResource($result['admin']))
                ->additional([
                    'message' => 'Admin updated successfully'
                ]);
        } catch (\Exception $e) {
            $this->logError($e, 'Error updating admin');
            return $this->handleException($e);
        }
    }

    /**
     * Delete admin
     */
    public function destroy(int $id)
    {
        try {
            $result = $this->adminService->deleteAdmin($id);

            return $this->successResponse([], $result['message']);
        } catch (\Exception $e) {
            $this->logError($e, 'Error deleting admin');
            return $this->handleException($e);
        }
    }

    /**
     * Force delete admin permanently
     */
    public function forceDelete(int $id)
    {
        try {
            $result = $this->adminService->forceDeleteAdmin($id);

            return $this->successResponse([], $result['message']);
        } catch (\Exception $e) {
            $this->logError($e, 'Error force deleting admin');
            return $this->handleException($e);
        }
    }

    /**
     * Restore soft deleted admin
     */
    public function restore(int $id)
    {
        try {
            $result = $this->adminService->restoreAdmin($id);

            return (new AdminResource($result['admin']))
                ->additional([
                    'message' => $result['message']
                ]);
        } catch (\Exception $e) {
            $this->logError($e, 'Error restoring admin');
            return $this->handleException($e);
        }
    }

    /**
     * Get soft deleted admins
     */
    public function trashed(Request $request)
    {
        try {
            $trashedAdmins = $this->adminService->getTrashedAdmins($request);

            return (new AdminCollection($trashedAdmins))
                ->additional([
                    'message' => 'Trashed admins list retrieved successfully'
                ]);
        } catch (\Exception $e) {
            $this->logError($e, 'Error fetching trashed admins');
            return $this->handleException($e);
        }
    }
}
