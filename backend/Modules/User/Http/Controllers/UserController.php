<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Requests\FindUserRequest;
use Modules\User\Http\Requests\UploadAvatarRequest;
use App\Services\Interfaces\ImageServiceInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserController extends BaseController
{
    protected UserRepositoryInterface $userRepository;
    protected ImageServiceInterface $imageService;

    public function __construct(UserRepositoryInterface $userRepository, ImageServiceInterface $imageService)
    {
        $this->userRepository = $userRepository;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of users (LIST)
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('viewAny', User::class);
            
            $users = $this->userRepository->all();
            $this->logInfo('Users list retrieved', ['count' => $users->count()]);
            
            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving users list');
        }
    }

    /**
     * Store a newly created user (CREATE)
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', User::class);
            
            $validated = $request->validated();
            $validated['password'] = bcrypt($validated['password']);
            
            $user = $this->userRepository->create($validated);
            
            $this->logInfo('User created', ['user_id' => $user->id]);
            
            return $this->createdResponse($user, 'User created successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Creating user');
        }
    }

    /**
     * Display the specified user (READ)
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('view', $user);
            
            $this->logInfo('User viewed', ['user_id' => $user->id]);
            
            return $this->successResponse($user, 'User retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving user');
        }
    }

    /**
     * Update the specified user (UPDATE)
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('update', $user);
            
            $validated = $request->validated();
            
            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }
            
            $updatedUser = $this->userRepository->update($id, $validated);
            
            $this->logInfo('User updated', ['user_id' => $user->id]);
            
            return $this->updatedResponse($updatedUser, 'User updated successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating user');
        }
    }

    /**
     * Remove the specified user (DELETE)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('delete', $user);
            
            $deleted = $this->userRepository->delete($id);
            
            if (!$deleted) {
                return $this->errorResponse('Failed to delete user', 500);
            }

            $this->logInfo('User deleted', ['user_id' => $user->id]);
            
            return $this->deletedResponse('User deleted successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Deleting user');
        }
    }

    /**
     * Find user by name
     */
    public function findByName(FindUserRequest $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', User::class);
            
            $validated = $request->validated();
            $user = $this->userRepository->findByName($validated['name']);
            
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->logInfo('User found by name', ['user_id' => $user->id, 'name' => $validated['name']]);
            
            return $this->successResponse($user, 'User found successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Finding user by name');
        }
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $user = $this->userRepository->findById($userId);
            
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('uploadAvatar', $user);
            
            $file = $request->file('avatar');
            $result = $this->imageService->uploadUserAvatar($file, $userId);

            if (!$result['success']) {
                return $this->errorResponse($result['message'], 400);
            }

            // Update user with new avatar path
            $this->userRepository->update($userId, ['avatar' => $result['data']['path']]);

            $this->logInfo('User avatar uploaded', ['user_id' => $userId]);
            
            return $this->successResponse($result['data'], 'Avatar uploaded successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Uploading user avatar');
        }
    }

    /**
     * Delete user avatar
     */
    public function deleteAvatar(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $user = $this->userRepository->findById($userId);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('uploadAvatar', $user);

            if ($user->avatar) {
                $this->imageService->deleteImage($user->avatar);
                $this->userRepository->update($userId, ['avatar' => null]);
            }

            $this->logInfo('User avatar deleted', ['user_id' => $userId]);
            
            return $this->successResponse(null, 'Avatar deleted successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Deleting user avatar');
        }
    }

    /**
     * Get current user profile
     */
    public function profile(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $user = $this->userRepository->findById($userId);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('view', $user);
            
            return $this->successResponse($user, 'Profile retrieved successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Retrieving user profile');
        }
    }

    /**
     * Update current user profile
     */
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $user = $this->userRepository->findById($userId);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $this->authorize('update', $user);
            
            $validated = $request->validated();
            
            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }
            
            $updatedUser = $this->userRepository->update($userId, $validated);
            
            $this->logInfo('User profile updated', ['user_id' => $userId]);
            
            return $this->updatedResponse($updatedUser, 'Profile updated successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating user profile');
        }
    }
}
