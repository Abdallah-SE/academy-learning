<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Exceptions\CustomException;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::query();

            // Apply filters
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve users: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created user (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'role' => ['required', Rule::in(['student', 'teacher', 'admin'])],
                'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
                'date_of_birth' => 'nullable|date',
                'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
                'country' => 'nullable|string|max:100',
                'timezone' => 'nullable|string|max:100',
                'preferences' => 'nullable|array'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['preferences'] = $validated['preferences'] ?? [
                'language' => 'en',
                'theme' => 'light',
                'notifications' => true
            ];

            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to create user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
                'phone' => 'nullable|string|max:20',
                'role' => ['sometimes', Rule::in(['student', 'teacher', 'admin'])],
                'status' => ['sometimes', Rule::in(['active', 'inactive', 'suspended'])],
                'date_of_birth' => 'nullable|date',
                'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
                'country' => 'nullable|string|max:100',
                'timezone' => 'nullable|string|max:100',
                'preferences' => 'nullable|array'
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user->fresh(),
                'message' => 'User updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to update user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified user (Admin only)
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            // Delete user avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to delete user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update current user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
                'country' => 'nullable|string|max:100',
                'timezone' => 'nullable|string|max:100',
                'preferences' => 'nullable|array'
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user->fresh(),
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to update profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = $request->user();

            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            
            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'data' => [
                    'avatar_url' => Storage::url($path)
                ],
                'message' => 'Avatar uploaded successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to upload avatar: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify WhatsApp number
     */
    public function verifyWhatsApp(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|max:20',
                'verification_code' => 'required|string|size:6'
            ]);

            $user = $request->user();

            // In a real application, you would verify the code
            // For now, we'll just mark it as verified
            $user->update([
                'whatsapp_verified' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp verified successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to verify WhatsApp: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get user statistics (Admin only)
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'students' => User::where('role', 'student')->count(),
                'teachers' => User::where('role', 'teacher')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'suspended_users' => User::where('status', 'suspended')->count(),
                'verified_whatsapp' => User::where('whatsapp_verified', true)->count(),
                'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve statistics: ' . $e->getMessage(), 500);
        }
    }
}
