'use client';

import { useState, useCallback } from 'react';
import { usePathname, useRouter } from 'next/navigation';

export interface MenuItem {
  icon: string;
  label: string;
  href: string;
  badge?: number | null;
  shortcut: string;
}

export interface UseSidebarProps {
  isCollapsed: boolean;
  onToggle: () => void;
}

export interface UseSidebarReturn {
  isToggling: boolean;
  currentPath: string;
  menuItems: MenuItem[];
  quickActions: Array<{
    id: string;
    label: string;
    icon: React.ReactNode;
    onClick: () => void;
  }>;
  handleToggle: () => void;
  handleMenuClick: (href: string, label: string) => void;
  handleLogout: () => void;
  handleThemeToggle: () => void;
  handleSettingsClick: () => void;
}

export const useSidebar = ({ isCollapsed, onToggle }: UseSidebarProps): UseSidebarReturn => {
  const [isToggling, setIsToggling] = useState(false);
  const router = useRouter();
  
  // Always call usePathname hook, but handle errors gracefully
  let pathname = '/';
  try {
    pathname = usePathname();
  } catch (error) {
    // Silently handle the error - this is expected during SSR
    pathname = '/';
  }

  const menuItems: MenuItem[] = [
    { icon: '📊', label: 'Dashboard', href: '/admin/dashboard', badge: null, shortcut: 'D' },
    { icon: '👥', label: 'Users', href: '/admin/users', badge: null, shortcut: 'U' },
    { icon: '👑', label: 'Admins', href: '/admin/admins', badge: null, shortcut: 'A' },
    { icon: '📚', label: 'Courses', href: '/admin/courses', badge: null, shortcut: 'C' },
    { icon: '💰', label: 'Revenue', href: '/admin/revenue', badge: null, shortcut: 'R' },
    { icon: '⚙️', label: 'Settings', href: '/admin/settings', badge: null, shortcut: 'S' },
    { icon: '📈', label: 'Analytics', href: '/admin/analytics', badge: null, shortcut: 'L' },
    { icon: '🔔', label: 'Notifications', href: '/admin/notifications', badge: null, shortcut: 'N' },
    { icon: '❓', label: 'Help', href: '/admin/help', badge: null, shortcut: 'H' },
  ];

  const handleToggle = useCallback(() => {
    setIsToggling(true);
    onToggle();
    // Reset toggling state after animation
    setTimeout(() => setIsToggling(false), 500);
  }, [onToggle]);

  const handleMenuClick = useCallback((href: string, label: string) => {
    // Only navigate if we're not already on the target page
    if (pathname !== href) {
      console.log(`Navigating to: ${href} (${label})`);
      router.push(href);
    }
  }, [pathname, router]);

  const handleLogout = useCallback(() => {
    // Add logout logic here
    console.log('Logging out...');
  }, []);

  const handleThemeToggle = useCallback(() => {
    console.log('Toggle theme');
  }, []);

  const handleSettingsClick = useCallback(() => {
    console.log('Open settings');
  }, []);

  const quickActions = [
    {
      id: 'theme',
      label: 'Theme',
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
      ),
      onClick: handleThemeToggle,
    },
    {
      id: 'settings',
      label: 'Settings',
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      ),
      onClick: handleSettingsClick,
    },
  ];

  return {
    isToggling,
    currentPath: pathname,
    menuItems,
    quickActions,
    handleToggle,
    handleMenuClick,
    handleLogout,
    handleThemeToggle,
    handleSettingsClick,
  };
};
