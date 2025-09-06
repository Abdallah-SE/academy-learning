<?php

namespace App\Http\Controllers;

use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\CustomException;

class MembershipPackageController extends Controller
{
    /**
     * Display a listing of membership packages
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MembershipPackage::query();

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Price range filter
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Duration filter
            if ($request->has('duration')) {
                $query->where('duration_days', $request->duration);
            }

            // Pagination
            $paginationParams = $this->getPaginationParams($request, 'membership');
            $packages = $query->paginate($paginationParams['per_page']);

            return response()->json([
                'success' => true,
                'data' => $packages,
                'message' => 'Membership packages retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve membership packages: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created membership package (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
                'features' => 'required|array',
                'features.quran_access' => 'boolean',
                'features.arabic_lessons' => 'boolean',
                'features.islamic_studies' => 'boolean',
                'features.homework_support' => 'boolean',
                'features.meeting_access' => 'boolean',
                'features.progress_tracking' => 'boolean',
                'status' => 'sometimes|in:active,inactive'
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            $package = MembershipPackage::create($validated);

            return response()->json([
                'success' => true,
                'data' => $package,
                'message' => 'Membership package created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to create membership package: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified membership package
     */
    public function show(MembershipPackage $package): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $package,
                'message' => 'Membership package retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve membership package: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified membership package (Admin only)
     */
    public function update(Request $request, MembershipPackage $package): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'duration_days' => 'sometimes|integer|min:1',
                'features' => 'sometimes|array',
                'features.quran_access' => 'boolean',
                'features.arabic_lessons' => 'boolean',
                'features.islamic_studies' => 'boolean',
                'features.homework_support' => 'boolean',
                'features.meeting_access' => 'boolean',
                'features.progress_tracking' => 'boolean',
                'status' => 'sometimes|in:active,inactive'
            ]);

            $package->update($validated);

            return response()->json([
                'success' => true,
                'data' => $package->fresh(),
                'message' => 'Membership package updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to update membership package: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified membership package (Admin only)
     */
    public function destroy(MembershipPackage $package): JsonResponse
    {
        try {
            // Check if package has active memberships
            if ($package->activeMemberships()->exists()) {
                throw new CustomException('Cannot delete package with active memberships', 400);
            }

            $package->delete();

            return response()->json([
                'success' => true,
                'message' => 'Membership package deleted successfully'
            ]);

        } catch (CustomException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CustomException('Failed to delete membership package: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get active membership packages for public display
     */
    public function publicPackages(): JsonResponse
    {
        try {
            $packages = MembershipPackage::where('status', 'active')
                ->orderBy('price', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $packages,
                'message' => 'Public packages retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve public packages: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get package comparison
     */
    public function compare(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'package_ids' => 'required|array|min:2|max:4',
                'package_ids.*' => 'integer|exists:membership_packages,id'
            ]);

            $packages = MembershipPackage::whereIn('id', $request->package_ids)
                ->orderBy('price', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $packages,
                'message' => 'Package comparison retrieved successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new CustomException('Validation failed', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve package comparison: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get package statistics (Admin only)
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_packages' => MembershipPackage::count(),
                'active_packages' => MembershipPackage::where('status', 'active')->count(),
                'inactive_packages' => MembershipPackage::where('status', 'inactive')->count(),
                'average_price' => MembershipPackage::avg('price'),
                'price_range' => [
                    'min' => MembershipPackage::min('price'),
                    'max' => MembershipPackage::max('price')
                ],
                'duration_options' => MembershipPackage::distinct('duration_days')->pluck('duration_days'),
                'popular_features' => $this->getPopularFeatures()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Package statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            throw new CustomException('Failed to retrieve package statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get popular features across packages
     */
    private function getPopularFeatures(): array
    {
        $packages = MembershipPackage::all();
        $features = [
            'quran_access' => 0,
            'arabic_lessons' => 0,
            'islamic_studies' => 0,
            'homework_support' => 0,
            'meeting_access' => 0,
            'progress_tracking' => 0
        ];

        foreach ($packages as $package) {
            foreach ($package->features as $feature => $enabled) {
                if ($enabled) {
                    $features[$feature]++;
                }
            }
        }

        return $features;
    }

    /**
     * Duplicate a membership package (Admin only)
     */
    public function duplicate(MembershipPackage $package): JsonResponse
    {
        try {
            $newPackage = $package->replicate();
            $newPackage->name = $package->name . ' (Copy)';
            $newPackage->status = 'inactive';
            $newPackage->save();

            return response()->json([
                'success' => true,
                'data' => $newPackage,
                'message' => 'Membership package duplicated successfully'
            ], 201);

        } catch (\Exception $e) {
            throw new CustomException('Failed to duplicate membership package: ' . $e->getMessage(), 500);
        }
    }
}
