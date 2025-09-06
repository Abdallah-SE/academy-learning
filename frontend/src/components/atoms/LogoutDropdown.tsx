'use client';

import React, { useState } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { useTranslations } from '@/hooks/useTranslations';
import { LogOutIcon, UserIcon, ChevronDownIcon, Loader2Icon } from 'lucide-react';
import { cn } from '@/lib/utils';

interface LogoutDropdownProps {
  /**
   * User information to display
   */
  user?: {
    name?: string;
    email?: string;
    avatar?: string;
  };
  /**
   * The variant/style of the dropdown
   */
  variant?: 'default' | 'minimal' | 'profile';
  /**
   * Custom className for styling
   */
  className?: string;
  /**
   * Whether to show user info
   */
  showUserInfo?: boolean;
  /**
   * Whether to show loading state
   */
  showLoading?: boolean;
  /**
   * Translation namespace (defaults to 'common')
   */
  translationNamespace?: 'common' | 'admin' | 'dashboard';
  /**
   * Callback function called before logout
   */
  onBeforeLogout?: () => void | Promise<void>;
  /**
   * Callback function called after logout
   */
  onAfterLogout?: () => void;
}

export const LogoutDropdown: React.FC<LogoutDropdownProps> = ({
  user,
  variant = 'default',
  className,
  showUserInfo = true,
  showLoading = true,
  translationNamespace = 'common',
  onBeforeLogout,
  onAfterLogout,
}) => {
  const { logout, isLoading } = useAuthContext();
  const t = useTranslations(translationNamespace);
  const [isOpen, setIsOpen] = useState(false);
  const [isLoggingOut, setIsLoggingOut] = useState(false);

  const handleLogout = async () => {
    if (isLoggingOut) return;

    try {
      setIsLoggingOut(true);
      setIsOpen(false);
      
      // Call before logout callback if provided
      if (onBeforeLogout) {
        await onBeforeLogout();
      }

      // Perform logout
      await logout();

      // Call after logout callback if provided
      if (onAfterLogout) {
        onAfterLogout();
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      setIsLoggingOut(false);
    }
  };

  const isButtonLoading = (showLoading && isLoading) || isLoggingOut;

  // Base classes
  const baseClasses = 'relative inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

  // Variant classes
  const variantClasses = {
    default: 'px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:ring-blue-500',
    minimal: 'px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg focus:ring-gray-500',
    profile: 'px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 hover:border-gray-300 focus:ring-blue-500',
  };

  const buttonClasses = cn(
    baseClasses,
    variantClasses[variant],
    className
  );

  return (
    <div className="relative">
      <button
        onClick={() => setIsOpen(!isOpen)}
        disabled={isButtonLoading}
        className={buttonClasses}
      >
        {variant === 'profile' && showUserInfo && user && (
          <div className="flex items-center space-x-2">
            <div className="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
              <UserIcon className="w-3 h-3 text-white" />
            </div>
            <span className="truncate max-w-24">{user.name || user.email}</span>
          </div>
        )}
        
        {variant !== 'profile' && (
          <span>{t('logout')}</span>
        )}
        
        <ChevronDownIcon className={cn(
          "w-4 h-4 ml-1 transition-transform duration-200",
          isOpen && "rotate-180"
        )} />
      </button>

      {isOpen && (
        <>
          {/* Backdrop */}
          <div 
            className="fixed inset-0 z-10" 
            onClick={() => setIsOpen(false)}
          />
          
          {/* Dropdown */}
          <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
            {showUserInfo && user && (
              <div className="px-4 py-3 border-b border-gray-100">
                <div className="flex items-center space-x-3">
                  <div className="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <UserIcon className="w-4 h-4 text-white" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900 truncate">
                      {user.name || 'User'}
                    </p>
                    {user.email && (
                      <p className="text-xs text-gray-500 truncate">
                        {user.email}
                      </p>
                    )}
                  </div>
                </div>
              </div>
            )}
            
            <div className="py-1">
              <button
                onClick={handleLogout}
                disabled={isButtonLoading}
                className="w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
              >
                {isButtonLoading ? (
                  <Loader2Icon className="w-4 h-4 animate-spin" />
                ) : (
                  <LogOutIcon className="w-4 h-4" />
                )}
                <span>{t('logout')}</span>
              </button>
            </div>
          </div>
        </>
      )}
    </div>
  );
};

LogoutDropdown.displayName = 'LogoutDropdown';
