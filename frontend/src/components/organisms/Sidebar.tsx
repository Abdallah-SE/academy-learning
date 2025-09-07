import React from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useTranslations } from '@/hooks/useTranslations';
import { Logo } from '@/components/atoms/Logo';

interface SidebarProps {
  isCollapsed: boolean;
  onToggle: () => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ isCollapsed, onToggle }) => {
  const t = useTranslations('sidebar');
  const pathname = usePathname();
  const [isToggling, setIsToggling] = React.useState(false);

  const menuItems = [
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

  const handleLogout = () => {
    // Add logout logic here
    console.log('Logging out...');
  };

  const handleToggle = () => {
    setIsToggling(true);
    onToggle();
    // Reset toggling state after animation
    setTimeout(() => setIsToggling(false), 500);
  };

  const handleMenuClick = (href: string, label: string) => {
    console.log(`Navigating to: ${href} (${label})`);
    console.log('Current pathname:', pathname);
  };

  // Function to check if a menu item is active
  const isActive = (href: string) => {
    if (href === '/admin/dashboard') {
      return pathname === '/admin/dashboard';
    }
    return pathname.startsWith(href);
  };

  // Function to get tooltip text for collapsed state
  const getTooltip = (label: string) => {
    return isCollapsed ? label : '';
  };

  return (
    <div className={`bg-gradient-to-b from-white to-gray-50/30 border-r border-gray-200 shadow-lg transition-all duration-500 ease-in-out relative overflow-hidden ${
      isCollapsed ? 'w-16' : 'w-64'
    } ${isToggling ? 'animate-pulse' : ''}`}>
      {/* Animated Background Pattern */}
      <div className="absolute inset-0 opacity-5">
        <div className="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-400 via-purple-400 to-pink-400 animate-pulse"></div>
            </div>
      
      {/* Fallback Toggle Button for Collapsed State */}
      {isCollapsed && (
        <div className="absolute top-2 right-2 z-50">
        <button
            onClick={handleToggle}
            className="p-2 rounded-xl bg-white/90 hover:bg-white shadow-md hover:shadow-lg border border-gray-200/50 hover:border-gray-300 transition-all duration-300 group hover:scale-110 active:scale-95"
            title="Expand sidebar"
        >
            <svg className="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition-all duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      )}
      {/* Header */}
      <div className="h-24 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 shadow-sm relative">
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
            <button
              onClick={handleToggle}
              className={`relative p-3 rounded-2xl bg-gradient-to-br from-white via-blue-50 to-indigo-100 hover:from-blue-50 hover:via-indigo-100 hover:to-purple-100 shadow-lg hover:shadow-xl border border-blue-200/50 hover:border-blue-300 transition-all duration-500 group hover:scale-110 active:scale-95 overflow-hidden ${isToggling ? 'animate-pulse' : ''}`}
              title={isCollapsed ? "Expand sidebar" : "Collapse sidebar"}
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
        </div>
      </div>

      {/* Navigation */}
      <nav className="p-4 space-y-2">
        {menuItems.map((item, index) => {
          const active = isActive(item.href);
          return (
            <Link
              key={item.href}
              href={item.href}
              onClick={() => handleMenuClick(item.href, item.label)}
              title={getTooltip(`${item.label} (${item.shortcut})`)}
              className={`flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-300 ease-in-out group relative ${
                active
                  ? 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border-r-3 border-blue-600 font-medium shadow-sm transform scale-[1.02]'
                  : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-gray-900 hover:shadow-sm hover:scale-[1.01]'
              }`}
              style={{
                animationDelay: `${index * 50}ms`
              }}
            >
              <div className="flex items-center space-x-3">
                <span className={`text-lg transition-all duration-300 ${active ? 'scale-110 rotate-3' : 'group-hover:scale-110 group-hover:rotate-2'} ${!isCollapsed ? 'animate-in fade-in-0 slide-in-from-left-1 duration-300' : ''}`}>
                {item.icon}
              </span>
                {!isCollapsed && (
                  <span className={`font-medium transition-all duration-300 ${active ? 'translate-x-1' : 'group-hover:translate-x-1'} animate-in fade-in-0 slide-in-from-left-2 duration-300`}>
                    {item.label}
                  </span>
                )}
              </div>
              
              {/* Badge and Shortcut */}
              {!isCollapsed && (
                <div className="flex items-center space-x-2 animate-in fade-in-0 slide-in-from-right-1 duration-300 delay-100">
                  {item.badge && (
                    <span className="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">
                      {item.badge}
                    </span>
                  )}
                  <span className="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-gray-400 bg-gray-200 rounded group-hover:bg-gray-300 group-hover:text-gray-600 transition-all duration-200">
                    {item.shortcut}
                  </span>
                </div>
              )}
              
              {/* Collapsed state badge */}
              {isCollapsed && item.badge && (
                <div className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
              )}
            </Link>
          );
        })}
        
        {/* Logout Section */}
        <div className="pt-4 border-t border-gray-200">
          <button
            onClick={handleLogout}
            title={isCollapsed ? "Logout" : "Logout (Ctrl+Q)"}
            className="w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-300 ease-in-out group text-red-600 hover:bg-red-50 hover:text-red-700 hover:shadow-sm hover:scale-[1.01]"
            style={{
              animationDelay: `${menuItems.length * 50}ms`
            }}
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
      </nav>

      {/* Quick Actions */}
      {!isCollapsed && (
        <div className="px-4 py-3 border-t border-gray-200 animate-in fade-in-0 slide-in-from-bottom-1 duration-500">
          <div className="grid grid-cols-2 gap-2">
            <button className="flex items-center justify-center space-x-2 px-3 py-2 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <span>Theme</span>
            </button>
            <button className="flex items-center justify-center space-x-2 px-3 py-2 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>Settings</span>
            </button>
          </div>
        </div>
      )}

    </div>
  );
};
