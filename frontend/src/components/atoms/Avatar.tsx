'use client';

import React, { memo } from 'react';
import Image from 'next/image';
import { cn } from '@/lib/utils';

export interface AvatarProps {
  src?: string;
  alt: string;
  name: string;
  size?: 'sm' | 'md' | 'lg' | 'xl';
  className?: string;
  showFallback?: boolean;
  fallbackType?: 'initials' | 'logo';
}

const sizeClasses = {
  sm: 'w-8 h-8 text-xs',
  md: 'w-10 h-10 text-sm',
  lg: 'w-12 h-12 text-sm',
  xl: 'w-16 h-16 text-base',
};

const textSizeClasses = {
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-sm',
  xl: 'text-base',
};

export const Avatar: React.FC<AvatarProps> = memo(({
  src,
  alt,
  name,
  size = 'lg',
  className,
  showFallback = true,
  fallbackType = 'logo',
}) => {
  const initials = name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);

  const sizeClass = sizeClasses[size];
  const textSizeClass = textSizeClasses[size];

  // Check if src is a valid URL or if it's a default avatar that might not exist
  const hasValidSrc = src && !src.includes('default-avatar.png') && src.trim() !== '';

  return (
    <div className={cn(
      "flex items-center justify-center rounded-full border-2 border-gray-200 hover:border-blue-400 transition-all duration-200 shadow-sm hover:shadow-md flex-shrink-0",
      sizeClass,
      className
    )}>
      {hasValidSrc ? (
        <Image
          src={src}
          alt={alt}
          width={size === 'sm' ? 32 : size === 'md' ? 40 : size === 'lg' ? 48 : 64}
          height={size === 'sm' ? 32 : size === 'md' ? 40 : size === 'lg' ? 48 : 64}
          className="w-full h-full rounded-full object-cover"
          unoptimized={true}
          onError={(e) => {
            // If user avatar fails to load, show fallback
            const target = e.target as HTMLImageElement;
            const parent = target.parentElement;
            if (parent && showFallback) {
              if (fallbackType === 'logo') {
                parent.innerHTML = `
                  <div class="w-full h-full rounded-full bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 flex items-center justify-center">
                    <div class="w-3/5 h-3/5 flex items-center justify-center">
                      <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" class="text-white opacity-90">
                        <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                        <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
                      </svg>
                    </div>
                  </div>
                `;
              } else {
                parent.innerHTML = `
                  <div class="w-full h-full rounded-full bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 flex items-center justify-center">
                    <span class="text-white font-bold ${textSizeClass}">${initials}</span>
                  </div>
                `;
              }
            }
          }}
        />
      ) : showFallback ? (
        fallbackType === 'logo' ? (
          <div className="w-full h-full rounded-full bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 flex items-center justify-center relative">
            {/* Admin icon using CSS - more reliable than external image */}
            <div className="w-3/5 h-3/5 flex items-center justify-center">
              <svg 
                width="100%" 
                height="100%" 
                viewBox="0 0 24 24" 
                fill="none" 
                className="text-white opacity-90"
              >
                <path 
                  d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" 
                  fill="currentColor"
                />
                <path 
                  d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" 
                  fill="currentColor"
                />
              </svg>
            </div>
          </div>
        ) : (
          <div className="w-full h-full rounded-full bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 flex items-center justify-center">
            <span className={cn("text-white font-bold", textSizeClass)}>
              {initials}
            </span>
          </div>
        )
      ) : (
        <div className="w-full h-full rounded-full bg-gray-200 flex items-center justify-center">
          <span className={cn("text-gray-500 font-medium", textSizeClass)}>
            ?
          </span>
        </div>
      )}
    </div>
  );
});

Avatar.displayName = 'Avatar';
