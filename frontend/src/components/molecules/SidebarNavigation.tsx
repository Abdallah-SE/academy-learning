'use client';

import React from 'react';
import { SidebarMenuItem, SidebarLogoutButton } from '@/components/atoms';

export interface MenuItem {
  icon: string;
  label: string;
  href: string;
  badge?: number | null;
  shortcut: string;
}

export interface SidebarNavigationProps {
  menuItems: MenuItem[];
  isCollapsed: boolean;
  activePath: string;
  onMenuClick?: (href: string, label: string) => void;
  onLogout: () => void;
  className?: string;
}

export const SidebarNavigation: React.FC<SidebarNavigationProps> = ({
  menuItems,
  isCollapsed,
  activePath,
  onMenuClick,
  onLogout,
  className = ''
}) => {
  const isActive = (href: string) => {
    if (href === '/admin/dashboard') {
      return activePath === '/admin/dashboard';
    }
    return activePath.startsWith(href);
  };

  return (
    <nav className={`p-4 space-y-2 ${className}`}>
      {menuItems.map((item, index) => {
        const active = isActive(item.href);
        return (
          <SidebarMenuItem
            key={item.href}
            icon={item.icon}
            label={item.label}
            href={item.href}
            badge={item.badge}
            shortcut={item.shortcut}
            isActive={active}
            isCollapsed={isCollapsed}
            onClick={() => onMenuClick?.(item.href, item.label)}
            style={{
              animationDelay: `${index * 50}ms`
            }}
          />
        );
      })}
      
      {/* Logout Section */}
      <SidebarLogoutButton
        isCollapsed={isCollapsed}
        onClick={onLogout}
      />
    </nav>
  );
};
