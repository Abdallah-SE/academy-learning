import React, { useState } from 'react';
import { Sidebar } from './Sidebar';
import { Breadcrumb } from '@/components/atoms/Breadcrumb';
import { useBreadcrumb } from '@/hooks/useBreadcrumb';

interface MainLayoutProps {
  children: React.ReactNode;
}

export const MainLayout: React.FC<MainLayoutProps> = ({ children }) => {
  const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);
  const { breadcrumbs } = useBreadcrumb();

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
