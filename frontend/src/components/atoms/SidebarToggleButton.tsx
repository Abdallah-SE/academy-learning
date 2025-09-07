'use client';

import React from 'react';

export interface SidebarToggleButtonProps {
  isCollapsed: boolean;
  isToggling: boolean;
  onClick: () => void;
  className?: string;
}

export const SidebarToggleButton: React.FC<SidebarToggleButtonProps> = ({
  isCollapsed,
  isToggling,
  onClick,
  className = ''
}) => {
  return (
    <div className={`relative ${className}`}>
      <button
        onClick={onClick}
        className={`relative p-3 rounded-2xl bg-gradient-to-br from-white via-blue-50 to-indigo-100 hover:from-blue-50 hover:via-indigo-100 hover:to-purple-100 shadow-lg hover:shadow-xl border border-blue-200/50 hover:border-blue-300 transition-all duration-500 group hover:scale-110 active:scale-95 overflow-hidden ${isToggling ? 'animate-pulse' : ''}`}
        title={isCollapsed ? "Expand sidebar" : "Collapse sidebar"}
        aria-label={isCollapsed ? "Expand sidebar" : "Collapse sidebar"}
      >
        {/* Animated Background */}
        <div className="absolute inset-0 bg-gradient-to-r from-blue-400/0 via-purple-400/0 to-pink-400/0 group-hover:from-blue-400/20 group-hover:via-purple-400/20 group-hover:to-pink-400/20 transition-all duration-700 rounded-2xl"></div>
        
        {/* Ripple Effect */}
        <div className="absolute inset-0 rounded-2xl overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-r from-blue-500/0 to-purple-500/0 group-active:from-blue-500/30 group-active:to-purple-500/30 transition-all duration-300 rounded-2xl scale-0 group-active:scale-150"></div>
        </div>
        
        {/* Icon Container */}
        <div className="relative z-10 flex items-center justify-center">
          {isCollapsed ? (
            <div className="relative">
              {/* Expand Icon with Morphing Animation */}
              <svg className="w-5 h-5 text-gray-700 group-hover:text-blue-600 transition-all duration-500 group-hover:scale-125 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M13 5l7 7-7 7M5 5l7 7-7 7" />
              </svg>
              {/* Floating Particles */}
              <div className="absolute -top-1 -right-1 w-1 h-1 bg-blue-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-opacity duration-300"></div>
              <div className="absolute -bottom-1 -left-1 w-1 h-1 bg-purple-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-opacity duration-500 delay-100"></div>
            </div>
          ) : (
            <div className="relative">
              {/* Collapse Icon with Morphing Animation */}
              <svg className="w-5 h-5 text-gray-700 group-hover:text-purple-600 transition-all duration-500 group-hover:scale-125 group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
              </svg>
              {/* Floating Particles */}
              <div className="absolute -top-1 -left-1 w-1 h-1 bg-purple-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-opacity duration-300"></div>
              <div className="absolute -bottom-1 -right-1 w-1 h-1 bg-pink-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-opacity duration-500 delay-100"></div>
            </div>
          )}
        </div>
        
        {/* Glow Effect */}
        <div className="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-400/0 via-purple-400/0 to-pink-400/0 group-hover:from-blue-400/10 group-hover:via-purple-400/10 group-hover:to-pink-400/10 transition-all duration-700 blur-sm"></div>
      </button>
      
      {/* Floating Tooltip */}
      <div className="absolute -top-12 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none">
        <div className="bg-gray-900 text-white text-xs px-3 py-2 rounded-lg shadow-lg whitespace-nowrap">
          {isCollapsed ? "Expand Sidebar" : "Collapse Sidebar"}
          <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
      </div>
    </div>
  );
};
