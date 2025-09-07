'use client';

import React from 'react';

export interface SidebarFallbackToggleProps {
  onClick: () => void;
  className?: string;
}

export const SidebarFallbackToggle: React.FC<SidebarFallbackToggleProps> = ({
  onClick,
  className = ''
}) => {
  return (
    <div className={`absolute top-2 right-2 z-50 ${className}`}>
      <button
        onClick={onClick}
        className="p-2 rounded-xl bg-white/90 hover:bg-white shadow-md hover:shadow-lg border border-gray-200/50 hover:border-gray-300 transition-all duration-300 group hover:scale-110 active:scale-95"
        title="Expand sidebar"
        aria-label="Expand sidebar"
      >
        <svg className="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition-all duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  );
};
