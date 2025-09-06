'use client';

import React, { useEffect, useMemo } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { useRouter } from 'next/navigation';
import { MainLayout } from '@/components/organisms/MainLayout';
import { AdminDataTable } from '@/components/organisms/AdminDataTable/AdminDataTable';
import { Loading } from '@/components/atoms/Loading';
import { LanguageSelector } from '@/components/atoms/LanguageSelector';
import { useAdminList } from '@/hooks/useAdminList';
import { Admin, AdminListActions } from '@/types/admin';
import { PlusIcon, RefreshCwIcon } from 'lucide-react';
import { usePageBreadcrumb } from '@/hooks/useBreadcrumb';

export default function AdminsPage() {
  const { isAuthenticated, isInitializing, logout } = useAuthContext();
  const router = useRouter();

  const {
    state,
    actions,
    deleteAdminMutation,
  } = useAdminList();

  // Set custom breadcrumb for this page
  const pageBreadcrumbs = useMemo(() => [
    {
      label: 'Admins',
      href: '/admin/admins',
      icon: <PlusIcon className="w-4 h-4" />,
      isActive: true,
      isClickable: false,
    }
  ], []);

  usePageBreadcrumb(pageBreadcrumbs);

  useEffect(() => {
    if (!isInitializing && !isAuthenticated) {
      router.push('/admin/login');
    }
  }, [isAuthenticated, isInitializing, router]);

  const handleEdit = (admin: Admin) => {
    // TODO: Implement edit functionality
    console.log('Edit admin:', admin);
  };

  const handleDelete = async (admin: Admin) => {
    if (window.confirm(`Are you sure you want to delete ${admin.name}?`)) {
      try {
        await deleteAdminMutation.mutateAsync(admin.id);
      } catch (err) {
        console.error('Error deleting admin:', err);
      }
    }
  };

  const handleView = (admin: Admin) => {
    // TODO: Implement view functionality
    console.log('View admin:', admin);
  };

  const handleCreateAdmin = () => {
    // TODO: Implement create admin functionality
    console.log('Create new admin');
  };


  const handleBulkDelete = async () => {
    if (state.selectedAdmins.length === 0) return;
    
    if (window.confirm(`Are you sure you want to delete ${state.selectedAdmins.length} selected admins?`)) {
      try {
        await actions.handleBulkDelete();
      } catch (err) {
        console.error('Error deleting admins:', err);
      }
    }
  };

  const adminActions: AdminListActions = {
    onCreate: handleCreateAdmin,
    onEdit: handleEdit,
    onDelete: handleDelete,
    onView: handleView,
    onBulkDelete: handleBulkDelete,
    onRefresh: actions.handleRefresh,
  };

  if (isInitializing) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loading />
      </div>
    );
  }

  if (!isAuthenticated) {
    return null;
  }

  return (
    <MainLayout>
      {/* Header */}
      <header className="bg-white border-b border-gray-200 shadow-sm">
        <div className="flex items-center justify-between h-16 px-6">
          <div className="flex items-center space-x-4">
            <h1 className="text-xl font-semibold text-gray-900">
              Admin Management
            </h1>
            <span className="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
              {state.pagination.total} Total
            </span>
          </div>
          
          <div className="flex items-center space-x-4">
            <LanguageSelector />
            <div className="h-6 w-px bg-gray-300"></div>
            <button
              onClick={logout}
              className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            >
              Logout
            </button>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 overflow-auto">
        <div className="max-w-7xl mx-auto py-8 px-6">
          {/* Action Bar */}
          <div className="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-4">
                {/* Search */}
                <div className="relative">
                  <input
                    type="text"
                    placeholder="Search by name, email, or username..."
                    value={state.filters.search || ''}
                    onChange={(e) => actions.handleSearch(e.target.value)}
                    className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-80"
                  />
                  <svg className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>

                {/* Status Filter */}
                <select
                  value={state.filters.status || ''}
                  onChange={(e) => actions.handleStatusFilter(e.target.value)}
                  className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="suspended">Suspended</option>
                </select>

                {/* Role Filter */}
                <select
                  value={state.filters.role || ''}
                  onChange={(e) => actions.handleRoleFilter(e.target.value)}
                  className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">All Roles</option>
                  <option value="super_admin">Super Admin</option>
                  <option value="admin">Admin</option>
                  <option value="moderator">Moderator</option>
                </select>
              </div>

              <div className="flex items-center space-x-2">
              
                <button
                  onClick={actions.handleRefresh}
                  disabled={state.isRefreshing}
                  className={`p-2 transition-colors ${
                    state.isRefreshing 
                      ? 'text-gray-300 cursor-not-allowed' 
                      : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg'
                  }`}
                  title={state.isRefreshing ? "Refreshing..." : "Refresh & Clear Filters"}
                >
                  <RefreshCwIcon className={`w-5 h-5 ${state.isRefreshing ? 'animate-spin' : ''}`} />
                </button>
                <button
                  onClick={handleCreateAdmin}
                  className="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                >
                  <PlusIcon className="w-4 h-4" />
                  Add Admin
                </button>
              </div>
            </div>
          </div>

          {/* Admin Data Table */}
          <AdminDataTable
            data={state.data}
            loading={state.loading}
            error={state.error}
            pagination={state.pagination}
            selectedAdmins={state.selectedAdmins}
            onSelectAdmin={actions.handleSelectAdmin}
            onSelectAll={actions.handleSelectAll}
            onPageChange={actions.handlePageChange}
            onPerPageChange={actions.handlePerPageChange}
            onRefresh={actions.handleRefresh}
            actions={adminActions}
          />
        </div>
      </main>
    </MainLayout>
  );
}
