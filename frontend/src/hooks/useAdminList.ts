import { useState, useCallback, useMemo } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { useAdmins, useDeleteAdmin } from './useAdmins';
import { Admin, AdminFilters, AdminListState, AdminListResponse } from '@/types/admin';
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

  const [selectedAdmins, setSelectedAdmins] = useState<number[]>([]);
  const [isRefreshing, setIsRefreshing] = useState(false);

  const admins = (adminsResponse as AdminListResponse)?.data || [];
  const pagination = useMemo(() => {
    // Handle both flat and nested pagination structures
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

  const handleSelectAdmin = useCallback((adminId: number, selected: boolean) => {
    setSelectedAdmins(prev => 
      selected 
        ? [...prev, adminId]
        : prev.filter(id => id !== adminId)
    );
  }, []);

  const handleSelectAll = useCallback((selected: boolean) => {
    setSelectedAdmins(selected ? admins.map((admin: Admin) => admin.id) : []);
  }, [admins]);

  const handleBulkDelete = useCallback(async () => {
    if (selectedAdmins.length === 0) return;
    
    try {
      await Promise.all(
        selectedAdmins.map(id => deleteAdminMutation.mutateAsync(id))
      );
      setSelectedAdmins([]);
    } catch (error) {
      console.error('Error deleting admins:', error);
    }
  }, [selectedAdmins, deleteAdminMutation]);

  const handleRefresh = useCallback(async () => {
    console.log('Refreshing admin data and clearing filters...');
    setIsRefreshing(true);
    try {
      // Reset all filters and search
      resetFilters();
      
      // Invalidate and refetch the admins query
      await queryClient.invalidateQueries({ queryKey: ['admins'] });
      await refetch();
      console.log('Admin data refreshed and filters cleared successfully');
    } catch (error) {
      console.error('Error refreshing admin data:', error);
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
    selectedAdmins,
    isRefreshing,
  };

  const actions = useMemo(() => ({
    handlePageChange,
    handlePerPageChange,
    handleSearch,
    handleStatusFilter,
    handleRoleFilter,
    handleSort,
    handleSelectAdmin,
    handleSelectAll,
    handleBulkDelete,
    handleRefresh,
    resetFilters,
  }), [
    handlePageChange,
    handlePerPageChange,
    handleSearch,
    handleStatusFilter,
    handleRoleFilter,
    handleSort,
    handleSelectAdmin,
    handleSelectAll,
    handleBulkDelete,
    handleRefresh,
    resetFilters,
  ]);

  return {
    state,
    actions,
    deleteAdminMutation,
  };
};