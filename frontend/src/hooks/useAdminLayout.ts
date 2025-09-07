'use client';

import { useAuthContext } from '@/context/AuthContext';
import { usePathname } from 'next/navigation';
import { useMemo } from 'react';

interface UseAdminLayoutReturn {
  isAuthenticated: boolean;
  isInitializing: boolean;
  isLoginPage: boolean;
  shouldShowMainLayout: boolean;
  shouldShowLoading: boolean;
}

export const useAdminLayout = (): UseAdminLayoutReturn => {
  const { isAuthenticated, isInitializing } = useAuthContext();
  const pathname = usePathname();
  
  return useMemo(() => {
    const isLoginPage = pathname === '/admin/login';
    const shouldShowMainLayout = isAuthenticated && !isLoginPage;
    const shouldShowLoading = isInitializing;
    
    return {
      isAuthenticated,
      isInitializing,
      isLoginPage,
      shouldShowMainLayout,
      shouldShowLoading,
    };
  }, [isAuthenticated, isInitializing, pathname]);
};
