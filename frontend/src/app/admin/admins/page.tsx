'use client';

import React, { useEffect, useMemo, useState } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { useLanguage } from '@/context/LanguageContext';
import { useTranslations } from '@/hooks/useTranslations';
import { useRouter } from 'next/navigation';
import { AdminDataTable } from '@/components/organisms/AdminDataTable/AdminDataTable';
import { Loading } from '@/components/atoms/Loading';
import { LanguageSelector } from '@/components/atoms/LanguageSelector';
import { LogoutButton, LogoutDropdown } from '@/components/atoms';
import { useAdminList } from '@/hooks/useAdminList';
import { Admin, AdminListActions } from '@/types/admin';
import { PlusIcon, RefreshCwIcon } from 'lucide-react';
import { usePageBreadcrumb } from '@/hooks/useBreadcrumb';
import { CreateAdminModalContainer } from '@/components/containers';

export default function AdminsPage() {
  const { isAuthenticated, isInitializing, user } = useAuthContext();
  const { currentLanguage } = useLanguage();
  const t = useTranslations('admin');
  const router = useRouter();
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);

  const {
    state,
    actions,
    deleteAdminMutation,
  } = useAdminList();

  // Set custom breadcrumb for this page
  const pageBreadcrumbs = useMemo(() => [
    {
      label: t('admins'),
      href: '/admin/admins',
      icon: <PlusIcon className="w-4 h-4" />,
      isActive: true,
      isClickable: false,
    }
  ], [t]);

  usePageBreadcrumb(pageBreadcrumbs);

  useEffect(() => {
    if (!isInitializing && !isAuthenticated) {
      router.replace('/admin/login');
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
    setIsCreateModalOpen(true);
  };

  const handleModalClose = () => {
    setIsCreateModalOpen(false);
  };

  const handleCreateSuccess = () => {
    // Refresh the admin list after successful creation
    actions.handleRefresh();
  };



  const adminActions: AdminListActions = {
    onCreate: handleCreateAdmin,
    onEdit: handleEdit,
    onDelete: handleDelete,
    onView: handleView,
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
    <>
      {/* Header */}
      <header className="bg-white border-b border-gray-200 shadow-sm">
        <div className="flex items-center justify-between h-16 px-6">
          <div className="flex items-center space-x-4">
            <h1 className="text-xl font-semibold text-gray-900">
              {t('title')}
            </h1>
          </div>
          
          <div className="flex items-center space-x-4">
            <LanguageSelector />
            <div className="h-6 w-px bg-gray-300"></div>
            <LogoutDropdown 
              user={{
                name: user?.name,
                email: user?.email,
                avatar: user?.avatar
              }}
              variant="profile"
              translationNamespace="admin"
            />
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
                    placeholder={t('searchPlaceholder')}
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
                  <option value="">{t('allStatus')}</option>
                  <option value="active">{t('active')}</option>
                  <option value="inactive">{t('inactive')}</option>
                  <option value="suspended">{t('suspended')}</option>
                </select>

                {/* Role Filter */}
                <select
                  value={state.filters.role || ''}
                  onChange={(e) => actions.handleRoleFilter(e.target.value)}
                  className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">{t('allRoles')}</option>
                  <option value="super_admin">{t('superAdmin')}</option>
                  <option value="admin">{t('admin')}</option>
                  <option value="moderator">{t('moderator')}</option>
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
                  {t('addAdmin')}
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
            onPageChange={actions.handlePageChange}
            onPerPageChange={actions.handlePerPageChange}
            onRefresh={actions.handleRefresh}
            actions={adminActions}
          />
        </div>
      </main>

      {/* Create Admin Modal */}
      <CreateAdminModalContainer
        isOpen={isCreateModalOpen}
        onClose={handleModalClose}
        onSuccess={handleCreateSuccess}
      />
    </>
  );
}
