<?php

namespace Modules\Auth\Services;

use Modules\Auth\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthService
{
    protected AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Login user
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $result = $this->authRepository->authenticate($credentials);

        if (!$result) {
            throw new ValidationException($validator, 'Invalid credentials');
        }

        return [
            'success' => true,
            'message' => 'Login successful',
            'data' => $result
        ];
    }

    /**
     * Register user
     *
     * @param array $userData
     * @return array
     * @throws ValidationException
     */
    public function register(array $userData): array
    {
        $validator = Validator::make($userData, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|string|in:user,admin'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            $result = $this->authRepository->register($userData);
            
            return [
                'success' => true,
                'message' => 'Registration successful',
                'data' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Refresh token
     *
     * @param string $token
     * @return array
     */
    public function refreshToken(string $token): array
    {
        $result = $this->authRepository->refreshToken($token);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Token refresh failed'
            ];
        }

        return [
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => $result
        ];
    }

    /**
     * Logout user
     *
     * @param string $token
     * @return array
     */
    public function logout(string $token): array
    {
        $result = $this->authRepository->logout($token);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Logout failed'
            ];
        }

        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }

    /**
     * Get user profile
     *
     * @param int $userId
     * @return array
     */
    public function getUserProfile(int $userId): array
    {
        $profile = $this->authRepository->getUserProfile($userId);

        if (!$profile) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        return [
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => $profile
        ];
    }

    /**
     * Update user profile
     *
     * @param int $userId
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function updateProfile(int $userId, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'sometimes|string|min:8|confirmed',
            'current_password' => 'required_with:password|string'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Verify current password if changing password
        if (isset($data['password'])) {
            $user = auth()->user();
            if (!Hash::check($data['current_password'], $user->password)) {
                throw new ValidationException($validator, 'Current password is incorrect');
            }
        }

        try {
            $result = $this->authRepository->updateProfile($userId, $data);
            
            return [
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if email exists
     *
     * @param string $email
     * @return array
     */
    public function checkEmailExists(string $email): array
    {
        $exists = $this->authRepository->userExistsByEmail($email);

        return [
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'Email already exists' : 'Email is available'
        ];
    }
}

