import { useState, useCallback, useMemo } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { useAdmins, useDeleteAdmin } from './useAdmins';
import { AdminListState, AdminListResponse } from '@/types/admin';
import { usePagination } from './usePagination';

export const useAdminList = () => {
  const queryClient = useQueryClient();
  
  const {
    filters,
    handlePageChange,
    handlePerPageChange,
    handleSearch,
    handleStatusFilter,
    handleRoleFilter,
    handleSort,
    resetFilters,
  } = usePagination('ADMIN');

  const { data: adminsResponse, isLoading, error, refetch } = useAdmins(filters);
  const deleteAdminMutation = useDeleteAdmin();

  const [isRefreshing, setIsRefreshing] = useState(false);

  const admins = (adminsResponse as AdminListResponse)?.data || [];
  const pagination = useMemo(() => {
    const paginationData = (adminsResponse as AdminListResponse)?.pagination;
    return {
      current_page: paginationData?.current_page || 1,
      last_page: paginationData?.last_page || 1,
      per_page: paginationData?.per_page || 10,
      total: paginationData?.total || 0,
      from: paginationData?.from || 0,
      to: paginationData?.to || 0,
      has_more_pages: paginationData?.has_more_pages || false,
    };
  }, [adminsResponse]);

  const handleRefresh = useCallback(async () => {
    console.log('🔄 Refreshing admin data and clearing filters...');
    setIsRefreshing(true);
    
    try {
      // Reset all filters and search
      resetFilters();
      
      // Invalidate and refetch the admins query
      await queryClient.invalidateQueries({ queryKey: ['admins'] });
      const result = await refetch();
      
      console.log('✅ Admin data refreshed and filters cleared successfully', {
        totalAdmins: result.data?.data?.length || 0,
        pagination: result.data?.pagination
      });
    } catch (error) {
      console.error('❌ Error refreshing admin data:', error);
    } finally {
      setIsRefreshing(false);
    }
  }, [queryClient, refetch, resetFilters]);

  const state: AdminListState = {
    data: admins,
    loading: isLoading,
    error: error?.message || null,
    pagination,
    filters,
    isRefreshing,
  };

  const actions = useMemo(() => ({
    handlePageChange,
    handlePerPageChange,
    handleSearch,
    handleStatusFilter,
    handleRoleFilter,
    handleSort,
    handleRefresh,
    resetFilters,
  }), [
    handlePageChange,
    handlePerPageChange,
    handleSearch,
    handleStatusFilter,
    handleRoleFilter,
    handleSort,
    handleRefresh,
    resetFilters,
  ]);

  return {
    state,
    actions,
    deleteAdminMutation,
  };
};