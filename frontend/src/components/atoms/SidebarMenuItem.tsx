'use client';

import React from 'react';
import Link from 'next/link';

export interface SidebarMenuItemProps {
  icon: string;
  label: string;
  href: string;
  badge?: number | null;
  shortcut: string;
  isActive: boolean;
  isCollapsed: boolean;
  onClick?: () => void;
  className?: string;
}

export const SidebarMenuItem: React.FC<SidebarMenuItemProps> = ({
  icon,
  label,
  href,
  badge,
  shortcut,
  isActive,
  isCollapsed,
  onClick,
  className = ''
}) => {
  const getTooltip = () => {
    return isCollapsed ? `${label} (${shortcut})` : '';
  };

  const handleClick = (e: React.MouseEvent<HTMLAnchorElement>) => {
    // Prevent default Link behavior if onClick handler is provided
    if (onClick) {
      e.preventDefault();
      onClick();
    }
  };

  return (
    <Link
      href={href}
      onClick={handleClick}
      title={getTooltip()}
      className={`flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-300 ease-in-out group relative ${
        isActive
          ? 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border-r-3 border-blue-600 font-medium shadow-sm transform scale-[1.02]'
          : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-gray-900 hover:shadow-sm hover:scale-[1.01]'
      } ${className}`}
    >
      <div className="flex items-center space-x-3">
        <span className={`text-lg transition-all duration-300 ${
          isActive ? 'scale-110 rotate-3' : 'group-hover:scale-110 group-hover:rotate-2'
        } ${!isCollapsed ? 'animate-in fade-in-0 slide-in-from-left-1 duration-300' : ''}`}>
          {icon}
        </span>
        {!isCollapsed && (
          <span className={`font-medium transition-all duration-300 ${
            isActive ? 'translate-x-1' : 'group-hover:translate-x-1'
          } animate-in fade-in-0 slide-in-from-left-2 duration-300`}>
            {label}
          </span>
        )}
      </div>
      
      {/* Badge and Shortcut */}
      {!isCollapsed && (
        <div className="flex items-center space-x-2 animate-in fade-in-0 slide-in-from-right-1 duration-300 delay-100">
          {badge && (
            <span className="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">
              {badge}
            </span>
          )}
          <span className="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-gray-400 bg-gray-200 rounded group-hover:bg-gray-300 group-hover:text-gray-600 transition-all duration-200">
            {shortcut}
          </span>
        </div>
      )}
      
      {/* Collapsed state badge */}
      {isCollapsed && badge && (
        <div className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
      )}
    </Link>
  );
};
