'use client';

import React, { memo } from 'react';
import { MainLayout } from '@/components/organisms/MainLayout';
import { AdminLayoutPresenter } from '@/components/presenters/AdminLayoutPresenter';
import { LoadingSpinner } from '@/components/atoms/LoadingSpinner';
import { useAdminLayout } from '@/hooks/useAdminLayout';
import { useAuthContext } from '@/context/AuthContext';

interface AdminLayoutContainerProps {
  children: React.ReactNode;
}

export const AdminLayoutContainer: React.FC<AdminLayoutContainerProps> = memo(({ children }) => {
  const {
    isLoginPage,
    shouldShowMainLayout,
    shouldShowLoading,
  } = useAdminLayout();
  
  const { error } = useAuthContext();
  
  // Show loading state while checking authentication
  if (shouldShowLoading) {
    return (
      <div className="flex h-screen bg-gray-50">
        <div className="flex-1 flex items-center justify-center">
          <LoadingSpinner size="md" text="Loading..." />
        </div>
      </div>
    );
  }

  // Show error state if there's a backend connection error
  if (error && !isLoginPage) {
    return (
      <div className="flex h-screen bg-gray-50">
        <div className="flex-1 flex items-center justify-center">
          <div className="text-center max-w-md mx-auto p-6">
            <div className="bg-red-50 border border-red-200 rounded-lg p-6">
              <div className="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <h3 className="text-lg font-medium text-red-800 mb-2">Connection Error</h3>
              <p className="text-red-600 mb-4">{error}</p>
              <button
                onClick={() => window.location.reload()}
                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                Retry
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // For login page, render children directly without MainLayout
  if (isLoginPage) {
    return <AdminLayoutPresenter>{children}</AdminLayoutPresenter>;
  }

  // For authenticated pages, wrap with MainLayout
  if (shouldShowMainLayout) {
    return (
      <MainLayout>
        <AdminLayoutPresenter>{children}</AdminLayoutPresenter>
      </MainLayout>
    );
  }

  // Fallback for unauthenticated non-login pages
  return <AdminLayoutPresenter>{children}</AdminLayoutPresenter>;
});

AdminLayoutContainer.displayName = 'AdminLayoutContainer';
