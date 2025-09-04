<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions
            $this->logException($e);
        });

        // Handle different types of exceptions
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions
     *
     * @param Throwable $e
     * @param Request $request
     * @return JsonResponse
     */
    protected function handleApiException(Throwable $e, Request $request): JsonResponse
    {
        // Handle validation exceptions
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        // Handle authentication exceptions
        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($e);
        }

        // Handle authorization exceptions
        if ($e instanceof AuthorizationException) {
            return $this->handleAuthorizationException($e);
        }

        // Handle model not found exceptions
        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($e);
        }

        // Handle query exceptions
        if ($e instanceof QueryException) {
            return $this->handleQueryException($e);
        }

        // Handle token mismatch exceptions
        if ($e instanceof TokenMismatchException) {
            return $this->handleTokenMismatchException($e);
        }

        // Handle HTTP exceptions
        if ($e instanceof HttpException) {
            return $this->handleHttpException($e);
        }

        // Handle not found exceptions
        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFoundException($e);
        }

        // Handle method not allowed exceptions
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedException($e);
        }

        // Handle all other exceptions
        return $this->handleGenericException($e);
    }

    /**
     * Handle validation exceptions
     *
     * @param ValidationException $e
     * @return JsonResponse
     */
    protected function handleValidationException(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
            'code' => 422,
            'timestamp' => now()->toISOString(),
        ], 422);
    }

    /**
     * Handle authentication exceptions
     *
     * @param AuthenticationException $e
     * @return JsonResponse
     */
    protected function handleAuthenticationException(AuthenticationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
            'code' => 401,
            'timestamp' => now()->toISOString(),
        ], 401);
    }

    /**
     * Handle authorization exceptions
     *
     * @param AuthorizationException $e
     * @return JsonResponse
     */
    protected function handleAuthorizationException(AuthorizationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
            'code' => 403,
            'timestamp' => now()->toISOString(),
        ], 403);
    }

    /**
     * Handle model not found exceptions
     *
     * @param ModelNotFoundException $e
     * @return JsonResponse
     */
    protected function handleModelNotFoundException(ModelNotFoundException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Resource not found',
            'code' => 404,
            'timestamp' => now()->toISOString(),
        ], 404);
    }

    /**
     * Handle query exceptions
     *
     * @param QueryException $e
     * @return JsonResponse
     */
    protected function handleQueryException(QueryException $e): JsonResponse
    {
        $message = config('app.debug') ? $e->getMessage() : 'Database error occurred';
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => 500,
            'timestamp' => now()->toISOString(),
        ], 500);
    }

    /**
     * Handle token mismatch exceptions
     *
     * @param TokenMismatchException $e
     * @return JsonResponse
     */
    protected function handleTokenMismatchException(TokenMismatchException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'CSRF token mismatch',
            'code' => 419,
            'timestamp' => now()->toISOString(),
        ], 419);
    }

    /**
     * Handle HTTP exceptions
     *
     * @param HttpException $e
     * @return JsonResponse
     */
    protected function handleHttpException(HttpException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage() ?: 'HTTP error occurred',
            'code' => $e->getStatusCode(),
            'timestamp' => now()->toISOString(),
        ], $e->getStatusCode());
    }

    /**
     * Handle not found exceptions
     *
     * @param NotFoundHttpException $e
     * @return JsonResponse
     */
    protected function handleNotFoundException(NotFoundHttpException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Route not found',
            'code' => 404,
            'timestamp' => now()->toISOString(),
        ], 404);
    }

    /**
     * Handle method not allowed exceptions
     *
     * @param MethodNotAllowedHttpException $e
     * @return JsonResponse
     */
    protected function handleMethodNotAllowedException(MethodNotAllowedHttpException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not allowed',
            'code' => 405,
            'timestamp' => now()->toISOString(),
        ], 405);
    }

    /**
     * Handle generic exceptions
     *
     * @param Throwable $e
     * @return JsonResponse
     */
    protected function handleGenericException(Throwable $e): JsonResponse
    {
        $message = config('app.debug') ? $e->getMessage() : 'An unexpected error occurred';
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => 500,
            'timestamp' => now()->toISOString(),
        ], 500);
    }

    /**
     * Log exception with context
     *
     * @param Throwable $e
     * @return void
     */
    protected function logException(Throwable $e): void
    {
        $logData = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'timestamp' => now()->toISOString(),
        ];

        Log::error('Exception: ' . $e->getMessage(), $logData);
    }
}
