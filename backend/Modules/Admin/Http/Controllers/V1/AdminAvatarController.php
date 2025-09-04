<?php

namespace Modules\Admin\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Services\ImageService;
use Modules\Admin\Services\AdminService;
use Modules\Admin\Http\Requests\V1\UploadAvatarRequest;
use Modules\Admin\Http\Resources\V1\AdminResource;
use Illuminate\Http\JsonResponse;

class AdminAvatarController extends BaseController
{
    protected $adminService;
    protected $imageService;

    public function __construct(AdminService $adminService, ImageService $imageService)
    {
        $this->adminService = $adminService;
        $this->imageService = $imageService;
    }

    /**
     * Upload/Update admin avatar
     */
    public function store(UploadAvatarRequest $request, int $adminId): JsonResponse
    {
        try {
            // Get admin
            $admin = $this->adminService->getAdminById($adminId);

            // Check if admin already has an avatar and delete it first
            if ($admin['admin']->avatar) {
                $this->imageService->deleteImage($admin['admin']->avatar);
            }

            // Upload avatar
            $file = $request->file('avatar');
            $result = $this->imageService->uploadAdminAvatar($file, $adminId);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            // Update admin with new avatar path
            $this->adminService->updateAdmin($adminId, ['avatar' => $result['data']['path']]);

            // Get updated admin
            $updatedAdmin = $this->adminService->getAdminById($adminId);

            return (new AdminResource($updatedAdmin['admin']))
                ->additional([
                    'avatar_url' => $result['data']['url'],
                    'avatar_path' => $result['data']['path'],
                    'message' => 'Avatar uploaded successfully'
                ])
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete admin avatar
     */
    public function destroy(int $adminId): JsonResponse
    {
        try {
            // Get admin
            $admin = $this->adminService->getAdminById($adminId);

            if ($admin['admin']->avatar) {
                // Delete old avatar file
                $this->imageService->deleteImage($admin['admin']->avatar);

                // Update admin to remove avatar
                $this->adminService->updateAdmin($adminId, ['avatar' => null]);
            }

            // Get updated admin
            $updatedAdmin = $this->adminService->getAdminById($adminId);

            return (new AdminResource($updatedAdmin['admin']))
                ->additional([
                    'message' => 'Avatar deleted successfully'
                ])
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting avatar: ' . $e->getMessage()
            ], 500);
        }
    }
}
