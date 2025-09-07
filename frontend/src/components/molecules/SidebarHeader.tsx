'use client';

import React from 'react';
import { Logo } from '@/components/atoms';
import { SidebarToggleButton, SidebarFallbackToggle } from '@/components/atoms';

export interface SidebarHeaderProps {
  isCollapsed: boolean;
  isToggling: boolean;
  onToggle: () => void;
  className?: string;
}

export const SidebarHeader: React.FC<SidebarHeaderProps> = ({
  isCollapsed,
  isToggling,
  onToggle,
  className = ''
}) => {
  return (
    <div className={`h-24 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 shadow-sm relative ${className}`}>
      {/* Fallback Toggle Button for Collapsed State */}
      {isCollapsed && <SidebarFallbackToggle onClick={onToggle} />}
      
      <div className="h-full flex items-center justify-between px-4">
        <div className="flex items-center justify-center flex-1 transition-all duration-500 ease-in-out">
          {!isCollapsed ? (
            <div className="flex flex-col items-center justify-center space-y-1 animate-in fade-in-0 slide-in-from-left-2 duration-500">
              <div className="relative">
                <Logo
                  src="/images/arabic-academic-logo.png"
                  alt="Arabic Academic"
                  width={200}
                  height={60}
                  className="h-14 w-auto drop-shadow-sm transition-all duration-500 ease-in-out"
                />
                <div className="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-indigo-600/5 rounded-lg"></div>
              </div>
              <div className="text-center animate-in fade-in-0 slide-in-from-bottom-1 duration-500 delay-200">
                <h1 className="text-sm font-semibold text-gray-800 tracking-wide">Arabic Academy</h1>
                <p className="text-xs text-gray-500 font-medium">Admin Dashboard</p>
              </div>
            </div>
          ) : (
            <div className="flex items-center justify-center w-full animate-in fade-in-0 zoom-in-95 duration-500">
              <div className="relative group">
                <Logo
                  src="/images/arabic-academic-logo.png"
                  alt="Arabic Academic"
                  width={48}
                  height={48}
                  className="h-12 w-12 drop-shadow-sm transition-all duration-500 ease-in-out group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-indigo-600/10 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
            </div>
          )}
        </div>
        
        {/* Toggle Button - Always visible */}
        <div className="relative z-50">
          <SidebarToggleButton
            isCollapsed={isCollapsed}
            isToggling={isToggling}
            onClick={onToggle}
          />
        </div>
      </div>
    </div>
  );
};
