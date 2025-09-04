<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Get the origin from the request
        $origin = $request->header('Origin');
        
        // Define allowed origins for both local and production
        $allowedOrigins = [
            // Local development
            'http://localhost:3000',
            'http://localhost:3001',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:3001',
            'http://localhost:3001',
            'http://127.0.0.1:3001',

            
            // Production domains (add your actual domains here)
            'https://yourdomain.com',
            'https://www.yourdomain.com',
            'https://app.yourdomain.com',
            
            // Staging/Testing (if you have them)
            'https://staging.yourdomain.com',
            'https://dev.yourdomain.com',
        ];

        // Check if the request origin is allowed
        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            // For development, allow localhost variations
            if (str_contains($origin, 'localhost') || str_contains($origin, '127.0.0.1')) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
            }
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN, Accept, Origin');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');

        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            $response->setStatusCode(200);
            $response->setContent('');
        }

        return $response;
    }
}
