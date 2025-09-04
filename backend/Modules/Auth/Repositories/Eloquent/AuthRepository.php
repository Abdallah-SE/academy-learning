<?php

namespace Modules\Auth\Repositories\Eloquent;

use Modules\Auth\Repositories\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class AuthRepository implements AuthRepositoryInterface
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Authenticate user with credentials
     *
     * @param array $credentials
     * @return array|null
     */
    public function authenticate(array $credentials): ?array
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return null;
            }

            $user = Auth::user();
            
            return [
                'user' => $user->toArray(),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ];
        } catch (JWTException $e) {
            \Log::error('JWT Authentication failed: ' . $e->getMessage());
            return null;
        } catch (Exception $e) {
            \Log::error('Authentication failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Register new user
     *
     * @param array $userData
     * @return array
     */
    public function register(array $userData): array
    {
        try {
            DB::beginTransaction();

            $user = $this->user->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'] ?? 'user'
            ]);

            $token = JWTAuth::fromUser($user);

            DB::commit();

            return [
                'user' => $user->toArray(),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('User registration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refresh JWT token
     *
     * @param string $token
     * @return array|null
     */
    public function refreshToken(string $token): ?array
    {
        try {
            $newToken = JWTAuth::refresh($token);
            
            return [
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ];
        } catch (JWTException $e) {
            \Log::error('Token refresh failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Logout user
     *
     * @param string $token
     * @return bool
     */
    public function logout(string $token): bool
    {
        try {
            JWTAuth::invalidate($token);
            return true;
        } catch (JWTException $e) {
            \Log::error('Logout failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user profile
     *
     * @param int $userId
     * @return array|null
     */
    public function getUserProfile(int $userId): ?array
    {
        try {
            $user = $this->user->findOrFail($userId);
            return $user->toArray();
        } catch (ModelNotFoundException $e) {
            \Log::warning('User not found: ' . $userId);
            return null;
        }
    }

    /**
     * Update user profile
     *
     * @param int $userId
     * @param array $data
     * @return array|null
     */
    public function updateProfile(int $userId, array $data): ?array
    {
        try {
            DB::beginTransaction();

            $user = $this->user->findOrFail($userId);
            
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);
            
            DB::commit();
            
            return $user->fresh()->toArray();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            \Log::warning('User not found for profile update: ' . $userId);
            return null;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Profile update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if user exists by email
     *
     * @param string $email
     * @return bool
     */
    public function userExistsByEmail(string $email): bool
    {
        return $this->user->where('email', $email)->exists();
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return array|null
     */
    public function findUserByEmail(string $email): ?array
    {
        try {
            $user = $this->user->where('email', $email)->first();
            return $user ? $user->toArray() : null;
        } catch (Exception $e) {
            \Log::error('Error finding user by email: ' . $e->getMessage());
            return null;
        }
    }
}

