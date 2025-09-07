'use client';

import React from 'react';

export interface SidebarLogoutButtonProps {
  isCollapsed: boolean;
  onClick: () => void;
  className?: string;
}

export const SidebarLogoutButton: React.FC<SidebarLogoutButtonProps> = ({
  isCollapsed,
  onClick,
  className = ''
}) => {
  return (
    <div className={`pt-4 border-t border-gray-200 ${className}`}>
      <button
        onClick={onClick}
        title={isCollapsed ? "Logout" : "Logout (Ctrl+Q)"}
        className="w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-300 ease-in-out group text-red-600 hover:bg-red-50 hover:text-red-700 hover:shadow-sm hover:scale-[1.01]"
        aria-label="Logout"
      >
        <span className="text-lg transition-all duration-300 group-hover:scale-110 group-hover:rotate-2">
          🚪
        </span>
        {!isCollapsed && (
          <span className="font-medium transition-all duration-300 group-hover:translate-x-1">
            Logout
          </span>
        )}
        {!isCollapsed && (
          <div className="ml-auto">
            <span className="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-red-400 bg-red-100 rounded group-hover:bg-red-200 group-hover:text-red-600 transition-all duration-200">
              Q
            </span>
          </div>
        )}
      </button>
    </div>
  );
};
