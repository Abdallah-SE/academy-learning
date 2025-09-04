<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomException;
use App\Traits\HasPermissions;
use Exception;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, HasPermissions;

    /**
     * Success response helper
     */
    protected function successResponse($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'status_code' => $status
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Error response helper
     */
    protected function errorResponse(string $message = 'Error', int $status = 400, array $errors = [], ?Exception $exception = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'status_code' => $status
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        // Log error if exception is provided
        if ($exception) {
            $this->logError($exception, $message);
        }

        return response()->json($response, $status);
    }

    /**
     * Validation error response helper
     */
    protected function validationErrorResponse(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Not found response helper
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Unauthorized response helper
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Forbidden response helper
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Server error response helper
     */
    protected function serverErrorResponse(string $message = 'Internal server error', ?Exception $exception = null): JsonResponse
    {
        return $this->errorResponse($message, 500, [], $exception);
    }

    /**
     * Paginated response helper
     */
    protected function paginatedResponse($data, string $message = 'Data retrieved successfully'): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'status_code' => 200,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages()
            ]
        ];

        return response()->json($response, 200);
    }

    /**
     * Created response helper
     */
    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Updated response helper
     */
    protected function updatedResponse($data, string $message = 'Resource updated successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Deleted response helper
     */
    protected function deletedResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }

    /**
     * Get current user helper
     */
    protected function getCurrentUser()
    {
        return Auth::user();
    }

    /**
     * Get current user ID helper
     */
    protected function getCurrentUserId(): ?int
    {
        return Auth::id();
    }

    /**
     * Check if user is authenticated helper
     */
    protected function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Get pagination parameters helper
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => (int) $request->get('per_page', 15),
            'page' => (int) $request->get('page', 1),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
            'search' => $request->get('search'),
            'filters' => $request->get('filters', [])
        ];
    }

    /**
     * Get search parameters helper
     */
    protected function getSearchParams(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'search_fields' => $request->get('search_fields', []),
            'filters' => $request->get('filters', []),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to')
        ];
    }

    /**
     * Validate pagination parameters helper
     */
    protected function validatePaginationParams(array $params): array
    {
        $validated = $params;

        // Ensure per_page is within reasonable limits
        $validated['per_page'] = max(1, min(100, $validated['per_page']));
        
        // Ensure page is positive
        $validated['page'] = max(1, $validated['page']);
        
        // Validate sort direction
        $validated['sort_direction'] = in_array(strtolower($validated['sort_direction']), ['asc', 'desc']) 
            ? strtolower($validated['sort_direction']) 
            : 'desc';

        return $validated;
    }

    /**
     * Handle exceptions in a consistent way
     */
    protected function handleException(Exception $exception, string $context = 'Controller operation'): JsonResponse
    {
        $message = $exception->getMessage();
        
        // Log the exception
 
        // Return appropriate response based on exception type
        if ($exception instanceof CustomException) {
            return $this->errorResponse($message, $exception->getCode() ?: 400);
        }

        // For other exceptions, return server error in production
        if (app()->environment('production')) {
            return $this->serverErrorResponse('An error occurred while processing your request');
        }

        // In development, return the actual error
        return $this->serverErrorResponse($message, $exception);
    }

    /**
     * Log error with context
     */
    protected function logError(Exception $exception, string $context = ''): void
    {
        $logData = [
            'context' => $context,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'user_id' => $this->getCurrentUserId(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::error('Controller Error: ' . $context, $logData);
    }

    /**
     * Log info with context
     */
    protected function logInfo(string $message, array $context = []): void
    {
        $logData = array_merge($context, [
            'user_id' => $this->getCurrentUserId(),
            'url' => request()->fullUrl(),
            'method' => request()->method()
        ]);

        Log::info($message, $logData);
    }

    /**
     * Get request IP address helper
     */
    protected function getClientIp(): string
    {
        return request()->ip();
    }

    /**
     * Get user agent helper
     */
    protected function getUserAgent(): string
    {
        return request()->userAgent() ?? 'Unknown';
    }

    /**
     * Check if request is AJAX helper
     */
    protected function isAjaxRequest(): bool
    {
        return request()->ajax() || request()->wantsJson();
    }

    /**
     * Get request headers helper
     */
    protected function getRequestHeaders(): array
    {
        return request()->headers->all();
    }
}
