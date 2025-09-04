<?php

namespace Modules\Auth\Repositories\Interfaces;

interface AuthRepositoryInterface
{
    /**
     * Authenticate user with credentials
     *
     * @param array $credentials
     * @return array|null
     */
    public function authenticate(array $credentials): ?array;

    /**
     * Register new user
     *
     * @param array $userData
     * @return array
     */
    public function register(array $userData): array;

    /**
     * Refresh JWT token
     *
     * @param string $token
     * @return array|null
     */
    public function refreshToken(string $token): ?array;

    /**
     * Logout user
     *
     * @param string $token
     * @return bool
     */
    public function logout(string $token): bool;

    /**
     * Get user profile
     *
     * @param int $userId
     * @return array|null
     */
    public function getUserProfile(int $userId): ?array;

    /**
     * Update user profile
     *
     * @param int $userId
     * @param array $data
     * @return array|null
     */
    public function updateProfile(int $userId, array $data): ?array;

    /**
     * Check if user exists by email
     *
     * @param string $email
     * @return bool
     */
    public function userExistsByEmail(string $email): bool;

    /**
     * Find user by email
     *
     * @param string $email
     * @return array|null
     */
    public function findUserByEmail(string $email): ?array;
}

