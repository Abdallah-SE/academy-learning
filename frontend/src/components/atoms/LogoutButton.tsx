'use client';

import React, { useState } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { useTranslations } from '@/hooks/useTranslations';
import { LogOutIcon, Loader2Icon } from 'lucide-react';
import { cn } from '@/lib/utils';

interface LogoutButtonProps {
  /**
   * The variant/style of the logout button
   */
  variant?: 'default' | 'ghost' | 'outline' | 'destructive';
  /**
   * The size of the button
   */
  size?: 'sm' | 'md' | 'lg';
  /**
   * Whether to show the logout icon
   */
  showIcon?: boolean;
  /**
   * Whether to show loading state
   */
  showLoading?: boolean;
  /**
   * Custom className for styling
   */
  className?: string;
  /**
   * Custom text for the button (overrides translation)
   */
  text?: string;
  /**
   * Whether the button should be disabled
   */
  disabled?: boolean;
  /**
   * Callback function called before logout
   */
  onBeforeLogout?: () => void | Promise<void>;
  /**
   * Callback function called after logout
   */
  onAfterLogout?: () => void;
  /**
   * Translation namespace (defaults to 'common')
   */
  translationNamespace?: 'common' | 'admin' | 'dashboard';
}

export const LogoutButton: React.FC<LogoutButtonProps> = ({
  variant = 'default',
  size = 'md',
  showIcon = true,
  showLoading = true,
  className,
  text,
  disabled = false,
  onBeforeLogout,
  onAfterLogout,
  translationNamespace = 'common',
}) => {
  const { logout, isLoading } = useAuthContext();
  const t = useTranslations(translationNamespace);
  const [isLoggingOut, setIsLoggingOut] = useState(false);

  const handleLogout = async () => {
    if (disabled || isLoggingOut) return;

    try {
      setIsLoggingOut(true);
      
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
  const buttonText = text || t('logout');

  // Base button classes
  const baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

  // Size classes
  const sizeClasses = {
    sm: 'px-3 py-1.5 text-xs rounded-md',
    md: 'px-4 py-2 text-sm rounded-lg',
    lg: 'px-6 py-3 text-base rounded-lg',
  };

  // Variant classes
  const variantClasses = {
    default: 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 hover:border-gray-400 focus:ring-blue-500',
    ghost: 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:ring-gray-500',
    outline: 'text-gray-700 bg-transparent border border-gray-300 hover:bg-gray-50 hover:border-gray-400 focus:ring-blue-500',
    destructive: 'text-white bg-red-600 border border-red-600 hover:bg-red-700 hover:border-red-700 focus:ring-red-500',
  };

  const buttonClasses = cn(
    baseClasses,
    sizeClasses[size],
    variantClasses[variant],
    className
  );

  return (
    <button
      onClick={handleLogout}
      disabled={disabled || isButtonLoading}
      className={buttonClasses}
      title={buttonText}
    >
      {isButtonLoading ? (
        <Loader2Icon className="w-4 h-4 animate-spin" />
      ) : showIcon ? (
        <LogOutIcon className="w-4 h-4" />
      ) : null}
      
      {showIcon && (
        <span className={isButtonLoading ? 'ml-2' : 'ml-2'}>
          {buttonText}
        </span>
      )}
      
      {!showIcon && (
        <span>{buttonText}</span>
      )}
    </button>
  );
};

LogoutButton.displayName = 'LogoutButton';
