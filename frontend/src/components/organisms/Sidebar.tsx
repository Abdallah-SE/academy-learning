import React from 'react';
import { SidebarPresenter } from '@/components/presenters/SidebarPresenter';
import { SidebarErrorBoundary } from '@/components/atoms';

interface SidebarProps {
  isCollapsed: boolean;
  onToggle: () => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ isCollapsed, onToggle }) => {
  return (
    <SidebarErrorBoundary>
      <SidebarPresenter
        isCollapsed={isCollapsed}
        onToggle={onToggle}
      />
    </SidebarErrorBoundary>
  );
};