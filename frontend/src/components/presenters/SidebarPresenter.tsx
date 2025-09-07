'use client';

import React from 'react';
import { SidebarHeader } from '@/components/molecules/SidebarHeader';
import { SidebarNavigation } from '@/components/molecules/SidebarNavigation';
import { SidebarQuickActions } from '@/components/molecules/SidebarQuickActions';
import { useSidebar } from '@/hooks/useSidebar';

export interface SidebarPresenterProps {
  isCollapsed: boolean;
  onToggle: () => void;
  className?: string;
}

export const SidebarPresenter: React.FC<SidebarPresenterProps> = ({
  isCollapsed,
  onToggle,
  className = ''
}) => {
  const {
    isToggling,
    currentPath,
    menuItems,
    quickActions,
    handleToggle,
    handleMenuClick,
    handleLogout,
  } = useSidebar({ isCollapsed, onToggle });

  return (
    <div className={`bg-gradient-to-b from-white to-gray-50/30 border-r border-gray-200 shadow-lg transition-all duration-500 ease-in-out relative overflow-hidden ${
      isCollapsed ? 'w-16' : 'w-64'
    } ${isToggling ? 'animate-pulse' : ''} ${className}`}>
      {/* Animated Background Pattern */}
      <div className="absolute inset-0 opacity-5">
        <div className="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-400 via-purple-400 to-pink-400 animate-pulse"></div>
      </div>
      
      {/* Header */}
      <SidebarHeader
        isCollapsed={isCollapsed}
        isToggling={isToggling}
        onToggle={handleToggle}
      />

      {/* Navigation */}
      <SidebarNavigation
        menuItems={menuItems}
        isCollapsed={isCollapsed}
        activePath={currentPath}
        onMenuClick={handleMenuClick}
        onLogout={handleLogout}
      />

      {/* Quick Actions */}
      <SidebarQuickActions
        actions={quickActions}
        isCollapsed={isCollapsed}
      />
    </div>
  );
};
