<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Auth\Services\AuthService;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Http\Requests\UpdateProfileRequest;
use Modules\Auth\Http\Requests\CheckEmailRequest;
use Modules\Auth\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            if ($result['success']) {
                return $this->successResponse($result['data'], $result['message']);
            }

            return $this->errorResponse($result['message'], 400);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Login validation failed');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * User registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            if ($result['success']) {
                return $this->createdResponse($result['data'], $result['message']);
            }

            return $this->errorResponse($result['message'], 400);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Registration validation failed');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Refresh JWT token
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return $this->errorResponse('Token not provided', 400);
            }

            $result = $this->authService->refreshToken($token);

            if ($result['success']) {
                return $this->successResponse($result['data'], $result['message']);
            }

            return $this->errorResponse($result['message'], 400);
        } catch (Exception $e) {
            return $this->serverErrorResponse('Token refresh failed: ' . $e->getMessage());
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return $this->errorResponse('Token not provided', 400);
            }

            $result = $this->authService->logout($token);

            if ($result['success']) {
                return $this->successResponse(null, $result['message']);
            }

            return $this->errorResponse($result['message'], 400);
        } catch (Exception $e) {
            return $this->serverErrorResponse('Logout failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user profile
     */
    public function profile(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $result = $this->authService->getUserProfile($userId);

            if ($result['success']) {
                return $this->successResponse($result['data'], $result['message']);
            }

            return $this->notFoundResponse($result['message']);
        } catch (Exception $e) {
            return $this->serverErrorResponse('Profile retrieval failed: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $result = $this->authService->updateProfile($userId, $request->validated());

            if ($result['success']) {
                return $this->updatedResponse($result['data'], $result['message']);
            }

            return $this->errorResponse($result['message'], 400);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Profile update validation failed');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Profile update failed: ' . $e->getMessage());
        }
    }

    /**
     * Get authenticated user
     */
    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return $this->unauthorizedResponse('User not authenticated');
            }

            return $this->successResponse(new UserResource($user), 'User retrieved successfully');
        } catch (Exception $e) {
            return $this->serverErrorResponse('User retrieval failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if email exists
     */
    public function checkEmail(CheckEmailRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->checkEmailExists($request->validated()['email']);

            return $this->successResponse($result['data'], $result['message']);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Email check validation failed');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Email check failed: ' . $e->getMessage());
        }
    }
}
