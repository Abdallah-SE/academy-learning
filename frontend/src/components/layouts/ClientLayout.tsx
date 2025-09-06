'use client';

import { AuthProvider } from '@/context/AuthContext';
import { BreadcrumbProvider } from '@/context/BreadcrumbContext';

export const ClientLayout: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  return (
    <AuthProvider>
      <BreadcrumbProvider>
        {children}
      </BreadcrumbProvider>
    </AuthProvider>
  );
};
