import { useState, useCallback } from 'react';
import { PAGINATION_CONFIG } from '@/config/pagination';

export interface PaginationFilters {
  page: number;
  per_page: number;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  search?: string;
  [key: string]: any;
}

export const usePagination = (module: keyof typeof PAGINATION_CONFIG.MODULES = 'ADMIN') => {
  const defaultPerPage = PAGINATION_CONFIG.MODULES[module].DEFAULT_PER_PAGE;
  
  const [filters, setFilters] = useState<PaginationFilters>({
    page: 1,
    per_page: defaultPerPage,
    sort_by: 'created_at',
    sort_order: 'desc',
  });

  const updateFilters = useCallback((newFilters: Partial<PaginationFilters>) => {
    setFilters(prev => ({
      ...prev,
      ...newFilters,
    }));
  }, []);

  const resetFilters = useCallback(() => {
    setFilters({
      page: 1,
      per_page: defaultPerPage,
      sort_by: 'created_at',
      sort_order: 'desc',
    });
  }, [defaultPerPage]);

  const handlePageChange = useCallback((page: number) => {
    updateFilters({ page });
  }, [updateFilters]);

  const handlePerPageChange = useCallback((per_page: number) => {
    updateFilters({ per_page, page: 1 });
  }, [updateFilters]);

  const handleSearch = useCallback((search: string) => {
    updateFilters({ search, page: 1 });
  }, [updateFilters]);

  const handleSort = useCallback((sort_by: string, sort_order: 'asc' | 'desc') => {
    updateFilters({ sort_by, sort_order, page: 1 });
  }, [updateFilters]);

  const handleStatusFilter = useCallback((status: string) => {
    updateFilters({ status: status || undefined, page: 1 });
  }, [updateFilters]);

  const handleRoleFilter = useCallback((role: string) => {
    updateFilters({ role: role || undefined, page: 1 });
  }, [updateFilters]);

  return {
    filters,
    updateFilters,
    resetFilters,
    handlePageChange,
    handlePerPageChange,
    handleSearch,
    handleSort,
    handleStatusFilter,
    handleRoleFilter,
    paginationOptions: PAGINATION_CONFIG.OPTIONS,
  };
};