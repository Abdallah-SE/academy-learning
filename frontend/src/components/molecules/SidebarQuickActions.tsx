'use client';

import React from 'react';

export interface QuickAction {
  id: string;
  label: string;
  icon: React.ReactNode;
  onClick: () => void;
}

export interface SidebarQuickActionsProps {
  actions: QuickAction[];
  isCollapsed: boolean;
  className?: string;
}

export const SidebarQuickActions: React.FC<SidebarQuickActionsProps> = ({
  actions,
  isCollapsed,
  className = ''
}) => {
  if (isCollapsed) return null;

  return (
    <div className={`px-4 py-3 border-t border-gray-200 animate-in fade-in-0 slide-in-from-bottom-1 duration-500 ${className}`}>
      <div className="grid grid-cols-2 gap-2">
        {actions.map((action) => (
          <button
            key={action.id}
            onClick={action.onClick}
            className="flex items-center justify-center space-x-2 px-3 py-2 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105"
            aria-label={action.label}
          >
            {action.icon}
            <span>{action.label}</span>
          </button>
        ))}
      </div>
    </div>
  );
};
