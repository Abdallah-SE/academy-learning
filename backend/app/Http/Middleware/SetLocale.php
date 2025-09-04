<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Get language from header, query param, or user preference
        $locale = $request->header('Accept-Language') 
                ?? $request->query('lang') 
                ?? $request->user()?->preferred_language 
                ?? 'en';
        
        // Validate locale
        if (in_array($locale, ['en', 'ar', 'de'])) {
            app()->setLocale($locale);
        }
        
        return $next($request);
    }
}
