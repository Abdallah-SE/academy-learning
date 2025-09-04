<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomException extends Exception
{
    /**
     * The exception code
     *
     * @var int
     */
    protected $code = 500;

    /**
     * The exception message
     *
     * @var string
     */
    protected $message = 'An unexpected error occurred';

    /**
     * The exception context
     *
     * @var array
     */
    protected $context = [];

    /**
     * Create a new custom exception instance
     *
     * @param string $message
     * @param int $code
     * @param array $context
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        array $context = [],
        Exception $previous = null
    ) {
        $this->message = $message ?: $this->message;
        $this->code = $code ?: $this->code;
        $this->context = $context;

        parent::__construct($this->message, $this->code, $previous);

        // Log the exception
        $this->logException();
    }

    /**
     * Log the exception with context
     *
     * @return void
     */
    protected function logException(): void
    {
        $logData = [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString(),
            'context' => $this->context,
            'timestamp' => now()->toISOString(),
        ];

        Log::error('Custom Exception: ' . $this->getMessage(), $logData);
    }

    /**
     * Get the exception context
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Render the exception into an HTTP response
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'timestamp' => now()->toISOString(),
        ];

        // Add context in development environment
        if (config('app.debug')) {
            $response['debug'] = [
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'context' => $this->context,
            ];
        }

        return response()->json($response, $this->getCode());
    }

    /**
     * Report the exception
     *
     * @return void
     */
    public function report(): void
    {
        // Additional reporting logic can be added here
        // For example, sending to external monitoring services
    }
}
