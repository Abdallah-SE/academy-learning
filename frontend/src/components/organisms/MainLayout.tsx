'use client';

import React, { useState, useEffect } from 'react';
import { Sidebar } from './Sidebar';
import { Breadcrumb } from '@/components/atoms/Breadcrumb';
import { useBreadcrumb } from '@/hooks/useBreadcrumb';

interface MainLayoutProps {
  children: React.ReactNode;
}

export const MainLayout: React.FC<MainLayoutProps> = ({ children }) => {
  const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);
  const [isClient, setIsClient] = useState(false);
  
  // Always call the hook, but handle the error inside the hook
  const { breadcrumbs } = useBreadcrumb();
  
  useEffect(() => {
    setIsClient(true);
  }, []);

  // Show loading state during hydration
  if (!isClient) {
    return (
      <div className="flex h-screen bg-gray-50">
        <div className="w-64 bg-white border-r border-gray-200"></div>
        <div className="flex-1 flex flex-col overflow-hidden">
          {children}
        </div>
      </div>
    );
  }

  return (
    <div className="flex h-screen bg-gray-50">
      <Sidebar
        isCollapsed={isSidebarCollapsed}
        onToggle={() => setIsSidebarCollapsed(!isSidebarCollapsed)}
      />
      <div className="flex-1 flex flex-col overflow-hidden">
        {/* Breadcrumb Navigation */}
        {breadcrumbs.length > 0 && (
          <div className="bg-white border-b border-gray-200 px-6 py-3">
            <Breadcrumb items={breadcrumbs} />
          </div>
        )}
        {children}
      </div>
    </div>
  );
};
