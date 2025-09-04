<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Admin\Repositories\Interfaces\MembershipRepositoryInterface;
use Modules\Admin\Http\Requests\StoreMembershipPackageRequest;
use Modules\Admin\Http\Requests\UpdateMembershipPackageRequest;
use Modules\Admin\Http\Requests\UploadMembershipImageRequest;
use App\Services\Interfaces\ImageServiceInterface;
use Illuminate\Http\JsonResponse;

class MembershipController extends BaseController
{
    protected MembershipRepositoryInterface $membershipRepository;
    protected ImageServiceInterface $imageService;

    public function __construct(MembershipRepositoryInterface $membershipRepository, ImageServiceInterface $imageService)
    {
        $this->membershipRepository = $membershipRepository;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of membership packages (LIST)
     */
    public function index(): JsonResponse
    {
        $packages = $this->membershipRepository->all();
        return $this->successResponse($packages, 'Membership packages retrieved successfully');
    }

    /**
     * Store a newly created membership package (CREATE)
     */
    public function store(StoreMembershipPackageRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $package = $this->membershipRepository->create($validated);
        return $this->createdResponse($package, 'Membership package created successfully');
    }

    /**
     * Display the specified membership package (READ)
     */
    public function show(int $id): JsonResponse
    {
        $package = $this->membershipRepository->findById($id);
        
        if (!$package) {
            return $this->notFoundResponse('Membership package not found');
        }

        return $this->successResponse($package, 'Membership package retrieved successfully');
    }

    /**
     * Update the specified membership package (UPDATE)
     */
    public function update(UpdateMembershipPackageRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        
        $package = $this->membershipRepository->update($id, $validated);
        return $this->updatedResponse($package, 'Membership package updated successfully');
    }

    /**
     * Remove the specified membership package (DELETE)
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->membershipRepository->delete($id);
        
        if (!$deleted) {
            return $this->notFoundResponse('Membership package not found');
        }

        return $this->deletedResponse('Membership package deleted successfully');
    }

    /**
     * Find package by name
     */
    public function findByName(string $name): JsonResponse
    {
        $package = $this->membershipRepository->findByName($name);
        
        if (!$package) {
            return $this->notFoundResponse('Membership package not found');
        }

        return $this->successResponse($package, 'Membership package found successfully');
    }

    /**
     * Upload membership package image
     */
    public function uploadImage(UploadMembershipImageRequest $request, int $id): JsonResponse
    {
        try {
            $package = $this->membershipRepository->findById($id);
            
            if (!$package) {
                return $this->notFoundResponse('Membership package not found');
            }

            $file = $request->file('image');
            $result = $this->imageService->uploadMembershipImage($file, $id);

            if (!$result['success']) {
                return $this->errorResponse($result['message'], 400);
            }

            // Update package with new image path
            $this->membershipRepository->update($id, ['image' => $result['data']['path']]);

            return $this->successResponse($result['data'], 'Package image uploaded successfully');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Image upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete membership package image
     */
    public function deleteImage(int $id): JsonResponse
    {
        try {
            $package = $this->membershipRepository->findById($id);
            
            if (!$package) {
                return $this->notFoundResponse('Membership package not found');
            }

            if ($package->image) {
                $this->imageService->deleteImage($package->image);
                $this->membershipRepository->update($id, ['image' => null]);
            }

            return $this->successResponse(null, 'Package image deleted successfully');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Image deletion failed: ' . $e->getMessage());
        }
    }
}

