<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasPagination
{
    /**
     * Get pagination parameters from request with validation
     */
    protected function getPaginationParams(Request $request, string $module = 'default'): array
    {
        $config = config('pagination');
        $moduleConfig = $config['modules'][$module] ?? [];
        
        $defaultPerPage = $moduleConfig['default_per_page'] ?? $config['default_per_page'];
        $maxPerPage = $config['max_per_page'];
        $minPerPage = $config['min_per_page'];
        
        $perPage = $request->get('per_page', $defaultPerPage);
        
        // Validate per_page parameter
        $perPage = max($minPerPage, min($maxPerPage, (int) $perPage));
        
        $page = max(1, (int) $request->get('page', 1));
        
        return [
            'per_page' => $perPage,
            'page' => $page,
        ];
    }
    
    /**
     * Get pagination options for frontend
     */
    protected function getPaginationOptions(): array
    {
        return config('pagination.options', [5, 10, 25, 50, 100]);
    }
    
    /**
     * Get default per page for a specific module
     */
    protected function getDefaultPerPage(string $module = 'default'): int
    {
        $config = config('pagination');
        $moduleConfig = $config['modules'][$module] ?? [];
        
        return $moduleConfig['default_per_page'] ?? $config['default_per_page'];
    }
    
    /**
     * Get simple pagination parameters (backward compatibility)
     */
    protected function getSimplePaginationParams(Request $request, string $module = 'default'): array
    {
        $paginationParams = $this->getPaginationParams($request, $module);
        
        return [
            'page' => $paginationParams['page'],
            'per_page' => $paginationParams['per_page'],
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
            'search' => $request->get('search'),
            'filters' => $request->get('filters', [])
        ];
    }
}
